<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use App\Models\Page;
use App\Models\Slider;
use App\Models\ProductCategory;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\GlobalSection;
use App\Models\Wishlist;
use App\Models\ContactSubmission;
use App\Models\Newsletter;
use App\Models\Author;
use App\Models\BlogPost as Post;
use App\Models\BlogCategory;
use App\Models\BlogPost;

use App\Services\CartService;
use App\Services\WishlistService;

use App\Models\BulkEnquiry;
use Jenssegers\Agent\Agent;

class HomeController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected WishlistService $wishlist
    ) {
    }

    public function index()
    {
        $page = Page::where('template', 'home')->first();
        $sliders = Slider::active()->get();

        $featuredCategories = ProductCategory::active()
            ->where('is_featured', true)
            ->take(10)
            ->get();

        $popularProducts = Product::active()
            ->with('variants')
            ->where('is_featured', true)
            ->take(10)
            ->get();
        $popularProducts = $this->wishlist->attachWishlistFlag($popularProducts);

        $braceletCategory = ProductCategory::where('slug', 'bracelets')->first();
        $braceletProducts = collect();

        if ($braceletCategory) {
            $braceletProducts = Product::active()
                ->with(['variants', 'variants.values'])
                //->where('is_featured', true)
                ->whereHas('categories', function ($q) use ($braceletCategory) {
                    $q->where('product_categories.id', $braceletCategory->id);
                })
                ->take(10)
                ->get();
            $braceletProducts = $this->wishlist->attachWishlistFlag($braceletProducts);
        }
        //dd($braceletProducts->count(),ProductCategory::where('slug','bracelets')->first()->products()->count());
        $newProducts = Product::active()
            ->with('variants')
            ->orderByDesc('id')
            ->take(10)
            ->get();
        $newProducts = $this->wishlist->attachWishlistFlag($newProducts);

        $whyChooseSections = GlobalSection::active()
            ->where('template', 0)
            ->orderBy('id')
            ->take(4)
            ->get();

        $globalSections = GlobalSection::active()
            ->where('template', 1)
            ->orderBy('id')
            ->take(2)
            ->get();

        $globalSectionFirst = $globalSections->first();
        $globalSectionSecond = $globalSections->skip(1)->first();

        $customizeBracelet = Product::active()
            ->with(['variants', 'galleries'])
            ->with(['reviews', 'categories', 'tags', 'galleries', 'variants.values.attribute', 'attributes.values', 'faqs'])
            ->find(1);

        return view('frontend.home', compact(
            'page',
            'sliders',
            'featuredCategories',
            'popularProducts',
            'braceletProducts',
            'newProducts',
            'globalSectionFirst',
            'globalSectionSecond',
            'customizeBracelet',
            'whyChooseSections',
        ));
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->first();

        if (!$page) {
            $page = Page::where('slug', '404')->first();
            return view('frontend.404', compact('page'));
        }

        $template = $page->template ?? 'default';

        if (!view()->exists("frontend.$template")) {
            $template = 'default';
        }

        return match ($template) {
            'cart', 'checkout' => $this->renderCartPage($page, $template),
            'wishlist' => $this->renderWishlistPage($page, $template),
            'shop' => $this->renderShopPage($page, $template),
            'order-success-failed' => $this->renderOrderSuccessFailedPage($page, $template),
            'auth' => $this->renderAuthPage($page),
            default => view("frontend.$template", compact('page')),
        };
    }

    private function renderCartPage(Page $page, string $template)
    {
        $cart = $this->cartService->getCart(auth()->id(), session()->getId());

        $billingAddress = null;
        $shippingAddress = null;

        if (auth()->check()) {
            $billingAddress = auth()->user()->addresses()
                ->where('type', 'billing')
                ->where('is_default', true)
                ->first();

            $shippingAddress = auth()->user()->addresses()
                ->where('type', 'shipping')
                ->where('is_default', true)
                ->first();
        }

        return view("frontend.$template", compact('page', 'cart', 'billingAddress', 'shippingAddress'));
    }

    private function renderWishlistPage(Page $page, string $template)
    {
        $wishlists = Wishlist::with('wishlistable')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view("frontend.$template", compact('page', 'wishlists'));
    }

    private function renderOrderSuccessFailedPage(Page $page, string $template)
    {
        $encryptedOrder = request('order');

        $order = null;

        if ($encryptedOrder) {
            try {
                $orderId = decrypt($encryptedOrder);

                $order = \App\Models\Order::with(['items', 'coupons'])
                    ->where('id', $orderId)
                    ->when(auth()->check(), function ($q) {
                        $q->where('user_id', auth()->id());
                    })
                    ->first();

            } catch (\Exception $e) {
                $order = null; // invalid or tampered ID
            }
        }

        // detect status
        $status = request()->has('success') ? 'success' : (request()->has('failed') ? 'failed' : null);

        return view("frontend.$template", compact(
            'page',
            'order',
            'status'
        ));
    }

    private function renderShopPage(Page $page, string $template)
    {
        $filters = $this->filterData();

        return view("frontend.$template", compact('page', 'filters'));
    }

    private function renderAuthPage(Page $page)
    {
        if (auth()->check()) {
            return redirect(route('profile.dashboard', absolute: false));
        }

        return view("frontend.auth.{$page->slug}", compact('page'));
    }

    public function search(Request $request)
    {
        $page = Page::where('slug', 'search')->first();
        $query = $request->input('q');
        //$products = Product::where('title', 'like', "%{$query}%")->paginate(12);
        $products = Product::where('title', 'ILIKE', "%{$query}%")->paginate(12);
        $products = $this->wishlist->attachWishlistFlag($products);

        return view('frontend.search', compact('products', 'query', 'page'));
    }

    public function productList($categories = null)
    {
        $segments = $categories ? explode('/', $categories) : [];
        $categorySlug = end($segments) ?: null;

        $category = ProductCategory::active()->where('slug', $categorySlug)->first();
        if (!$category) {
            $page = Page::where('slug', '404')->first();
            return view('frontend.404', compact('page'));
        }
        $filters = $this->filterData();

        return view('frontend.product-category', compact('category', 'filters', 'segments'));
    }

    public function productDetails($slug)
    {
        $product = Product::active()
            ->with(['reviews', 'categories', 'tags', 'galleries', 'variants.values.attribute', 'attributes.values', 'faqs'])
            ->where('slug', $slug)
            ->first();
        if (!$product) {
            $page = Page::where('slug', '404')->first();
            return view('frontend.404', compact('page'));
        }
        $categoryIds = $product->categories->pluck('id');
        $relatedProducts = Product::active()
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('product_categories.id', $categoryIds);
            })
            ->where('id', '!=', $product->id)
            ->limit(6)
            ->get();

        return view('frontend.product-details', compact('product', 'relatedProducts'));
    }

    public function blogList($categories = null)
    {
        $segments = $categories ? explode('/', $categories) : [];
        $categorySlug = end($segments) ?: null;

        $category = BlogCategory::where('slug', $categorySlug)->first();
        $posts = $category
            ? $category->posts()->paginate(10)
            : Post::paginate(10);

        return view('frontend.blog.list', compact('posts', 'category', 'segments'));
    }

    public function blogDetails($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        return view('frontend.blog.details', compact('post'));
    }

    public function sitemapXML()
    {
        $pages = Page::active()
            ->whereNotIn('id', [1])
            ->with('children')
            ->orderBy('title')
            ->get();

        $categories = BlogCategory::active()
            ->with('children.children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $articles = BlogPost::active()
            ->orderBy('title')
            ->get();

        $xml = $this->generateXml($pages, $categories, $articles);

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    private function generateXml($pages, $categories, $articles): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        foreach ($pages as $page) {
            $xml .= $this->urlTag(route('page', $page->slug), $page->updated_at, 'monthly', '0.7');

            foreach ($page->children ?? [] as $child) {
                $xml .= $this->urlTag(route('page', $child->slug), $child->updated_at, 'monthly', '0.6');
            }
        }

        foreach ($categories as $category) {
            $xml .= $this->urlTag(route('category', $category->full_slug ?? $category->slug), $category->updated_at, 'weekly', '0.6');

            foreach ($category->children ?? [] as $child) {
                $xml .= $this->urlTag(route('category', $child->full_slug ?? $child->slug), $child->updated_at, 'weekly', '0.55');

                foreach ($child->children ?? [] as $sub) {
                    $xml .= $this->urlTag(route('category', $sub->full_slug ?? $sub->slug), $sub->updated_at, 'weekly', '0.50');
                }
            }
        }

        foreach ($articles as $article) {
            $xml .= $this->urlTag(route('article', $article->slug), $article->updated_at, 'daily', '0.8');
        }

        $xml .= '</urlset>';

        return $xml;
    }

    private function urlTag(string $loc, $lastmod, string $freq, string $priority): string
    {
        $lastmodTag = $lastmod ? "<lastmod>{$lastmod->toAtomString()}</lastmod>" : '';

        return "
        <url>
            <loc>{$loc}</loc>
            {$lastmodTag}
            <changefreq>{$freq}</changefreq>
            <priority>{$priority}</priority>
        </url>";
    }

    private function filterData(): array
    {
        $minPrice = Product::query()
            ->whereNotNull('regular_price')
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->selectRaw('MIN(COALESCE(sale_price, regular_price)) as min_price')
            ->value('min_price') ?? 0;

        $maxPrice = Product::query()
            ->whereNotNull('regular_price')
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->selectRaw('MAX(COALESCE(sale_price, regular_price)) as max_price')
            ->value('max_price') ?? 0;

        return [
            'price' => [
                'min' => $minPrice,
                'max' => $maxPrice,
            ],
            'categories' => ProductCategory::active()
                ->whereNull('parent_id')
                ->withCount('products')
                ->with(['children' => fn($q) => $q->withCount('products')->where('status', 1)])
                ->get()
                ->toArray(),
            'special' => Product::where('is_special', 1)->latest()->first(),
        ];
    }

    public function load(Request $request)
    {
        $query = Product::query()->stock()->active();

        if (!empty($request->categories) && is_array($request->categories)) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->whereIn('product_categories.id', $request->categories);
            });
        }

        $minPrice = filled($request->min_price) ? (float) $request->min_price : null;
        $maxPrice = filled($request->max_price) ? (float) $request->max_price : null;

        if ($minPrice !== null) {
            $query->where(function ($q) use ($minPrice) {
                $q->where(function ($inner) use ($minPrice) {
                    $inner->whereNotNull('sale_price')->where('sale_price', '>=', $minPrice);
                })->orWhere(function ($inner) use ($minPrice) {
                    $inner->whereNull('sale_price')->where('regular_price', '>=', $minPrice);
                });
            });
        }

        if ($maxPrice !== null) {
            $query->where(function ($q) use ($maxPrice) {
                $q->where(function ($inner) use ($maxPrice) {
                    $inner->whereNotNull('sale_price')->where('sale_price', '<=', $maxPrice);
                })->orWhere(function ($inner) use ($maxPrice) {
                    $inner->whereNull('sale_price')->where('regular_price', '<=', $maxPrice);
                });
            });
        }

        $rating = filled($request->rating) ? (float) $request->rating : null;

        if ($rating !== null && $rating > 0) {
            $query->whereIn('id', function ($q) use ($rating) {
                $q->select('product_id')
                    ->from('product_reviews')
                    ->groupBy('product_id')
                    ->havingRaw('CAST(AVG(rating) AS FLOAT) >= ?', [$rating]);
            });
        }

        match ($request->sort) {
            'popular' => $query->orderByDesc('views'),
            'name-asc' => $query->orderBy('title'),
            'name-desc' => $query->orderByDesc('title'),
            'price-low' => $query->orderByRaw('COALESCE(sale_price, regular_price) ASC'),
            'price-high' => $query->orderByRaw('COALESCE(sale_price, regular_price) DESC'),
            default => $query->latest(),
        };

        $products = $query->paginate(12, ['*'], 'page', $request->page);

        try {
            return response()->json([
                'html' => view('components.frontend.product-list', compact('products'))->render(),
                'count' => $products->count(),
                'hasMore' => $products->hasMorePages(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function getVariantPrice(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'values' => 'required|array',
        ]);

        $product = Product::findOrFail($request->product_id);

        $variant = $product->variants()
            ->whereHas('values', function ($q) use ($request) {
                $q->whereIn('product_attribute_values.id', $request->values);
            }, '=', count($request->values))
            ->first();

        if (!$variant) {
            return response()->json(['found' => false]);
        }

        $regularPrice = $variant->regular_price ?? $product->regular_price;
        $salePrice = $variant->sale_price;

        return response()->json([
            'found' => true,
            'variant_id' => $variant->id,
            'regular_price' => $regularPrice,
            'sale_price' => $salePrice,
            'regular_formatted' => currencyformat($regularPrice),
            'sale_formatted' => $salePrice ? currencyformat($salePrice) : null,
            'stock' => $variant->stock ?? 0,
            'sku' => $variant->sku,
        ]);
    }

    public function addReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $exists = ProductReview::where('product_id', $request->product_id)
            ->where('email', $request->email)
            ->exists();

        if ($exists) {
            return response()->json(['status' => false, 'message' => 'You already reviewed this product.'], 400);
        }

        try {
            $review = ProductReview::create($request->only('product_id', 'name', 'email', 'rating', 'review'));

            return response()->json(['status' => true, 'message' => 'Review added successfully!', 'data' => $review]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }

    public function contactFormSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email:rfc,dns',
            'message' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            ContactSubmission::create($request->only('name', 'phone', 'email', 'message'));

            Mail::send('emails.contact', ['request' => $request], function ($mail) use ($request) {
                $mail->to('yashvir.pal@kalkine.co.in')
                    ->subject('New Contact Message: ' . $request->subject)
                    ->replyTo($request->email);
            });

            return response()->json([
                'status' => true,
                'message' => 'Thanks for reaching out! We\'ll get back to you soon.',
                'redirect_url' => route('page', 'thank-you'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function newsletterSubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns|unique:newsletters,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            Newsletter::create($request->only('email'));

            Mail::send('emails.newsletter', ['email' => $request->email], function ($mail) use ($request) {
                $mail->to($request->email)->subject('Thanks for Subscribing to Our Newsletter');
            });

            return response()->json([
                'status' => true,
                'message' => 'Subscription successful! You\'ll start receiving updates soon.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkEnquirySubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email:rfc,dns|max:150',
            //  'phone' => 'required|digits_between:8,15',
            'phone' => 'required',
            'company' => 'nullable|string|max:150',
            'products' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1|max:100000',
            'message' => 'required|string|min:5|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $agent = new Agent();

            $enquiry = BulkEnquiry::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'message' => $request->message,
                'products' => $request->products,
                'quantity' => $request->quantity,

                // 🔥 Tracking
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'device' => $agent->isMobile() ? 'Mobile' : 'Desktop',
            ]);

            //✅ Send mail with clean data
            // Mail::send('emails.bulkenquiry', [
            //     'enquiry' => $enquiry
            // ], function ($mail) use ($request) {
            //     $mail->to('yashvir.pal@kalkine.co.in')->subject('New Bulk Enquiry from ' . $request->name)->replyTo($request->email);
            // });

            Mail::send('emails.bulkenquiry', ['data' => $enquiry], function ($mail) {
                $mail->to('yashvir.pal@kalkine.co.in')
                    ->subject('New Bulk Enquiry Received');
            });
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Thanks! We will contact you soon.',
                'redirect_url' => route('page', 'thank-you'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage() // remove in production
            ], 500);
        }
    }

    public function searchProduct(Request $request)
    {
        $query = $request->input('q');

        $products = Product::where('title', 'ILIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'title']);

        return response()->json($products);
    }
}