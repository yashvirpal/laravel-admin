<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use App\Models\Page;
use App\Models\Slider;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\GlobalSection;
use App\Models\Wishlist;
use App\Models\ContactSubmission;
use App\Models\Newsletter;
use App\Models\SearchMeta;
use App\Models\Author;
use App\Models\BlogPost as Post;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Validator;



use Illuminate\Support\Facades\Mail;
use App\Services\CartService;
use App\Services\WishlistService;


class HomeController extends Controller
{
    public function __construct(protected CartService $cartService, protected WishlistService $wishlist)
    {
    }
    public function index()
    {
        // Load home page
        $page = Page::where('template', 'home')->first();

        // Active sliders
        $sliders = Slider::active()->get();

        // Featured categories (limit 10)
        $featuredCategories = ProductCategory::active()->where('is_featured', true)->take(10)->get();

        // Popular products (limit 10)
        $popularProducts = Product::active()->with('variants')->where('is_featured', true)->take(10)->get();
        //  $popularProducts = $this->cart->attachCartQtyToProducts($popularProducts);
        $popularProducts = $this->wishlist->attachWishlistFlag($popularProducts);


        // Bracelet products (limit 10)
        $braceletCategory = ProductCategory::where('slug', 'bracelets')->first();
        $braceletProducts = Product::active()
            ->with('variants')
            ->where('is_featured', true)
            ->whereHas('categories', function ($q) use ($braceletCategory) {
                $q->where('product_categories.id', $braceletCategory->id);
            })
            ->take(10)
            ->get();
        // $braceletProducts = $this->cart->attachCartQtyToProducts($braceletProducts);
        $braceletProducts = $this->wishlist->attachWishlistFlag($braceletProducts);

        // Latest 10 products
        $newProducts = Product::active()->with('variants')->orderBy('id', 'desc')->take(10)->get();
        //  $newProducts = $this->cart->attachCartQtyToProducts($newProducts);
        $newProducts = $this->wishlist->attachWishlistFlag($newProducts);

        $whyChooseSections = GlobalSection::active()
            ->where('template', 0)
            ->orderBy('id', 'asc')
            ->take(4) // get first 2 sections
            ->get();

        $globalSections = GlobalSection::active()
            ->where('template', 1)
            ->orderBy('id', 'asc')
            ->take(2) // get first 2 sections
            ->get();

        // Assign separately for clarity
        $globalSectionFirst = $globalSections->first();
        $globalSectionSecond = $globalSections->skip(1)->first();

        // Customize Bracelet (single product)
        $customizeBracelet = Product::active()->with('variants')->where('id', 1)->with(['galleries'])->find(1);
        //dd($customizeBracelet);

        return view(
            'frontend.home',
            compact(
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
            )
        );


    }


    public function page($slug)
    {
        $page = Page::where('slug', $slug)->first();
        if (!$page) {
            return response()->view('frontend.404', [], 404);
        } else {
            $template = $page->template ?? 'default';

            if (!view()->exists("frontend.$template")) {
                $template = 'default';
            } else if ($page->template == "cart" || $page->template == "checkout") {
                $cart = $this->cartService->getCart(
                    auth()->id(),
                    session()->getId()
                );

                //  $addresses = Auth::user()->addresses()->latest()->get();
                // dd($addresses);
                return view("frontend.$template", compact('page', 'cart'));
            } else if ($page->template == "wishlist") {
                $wishlists = Wishlist::with('wishlistable')->where('user_id', Auth::id())->latest()->get();
                return view("frontend.$template", compact('page', 'wishlists'));
            } else if ($page->template == "shop") {
                // $products = Product::active()->paginate(12);
                // $products = $this->cart->attachCartQtyToProducts($products);
                // $products = $this->wishlist->attachWishlistFlag($products);

                $filters = $this->filterData();

                return view("frontend.$template", compact('page', 'filters'));
            } else {
                if ($template == 'auth') {
                    if (Auth::check()) {
                        return redirect(route('dashboard', absolute: false));
                    }
                    return view("frontend.$template.$page->slug", compact('page'));
                }
                return view("frontend.$template", compact('page'));
            }
        }
    }
    public function search(Request $request)
    {
        $query = $request->input('q');
        $products = Product::where('title', 'like', "%{$query}%")->paginate(12);
        $products = $this->cart->attachCartQtyToProducts($products);
        $products = $this->wishlist->attachWishlistFlag($products);

        return view("frontend.search", compact('products', 'query'));
    }

    public function productList($categories = null)
    {
        $segments = $categories ? explode('/', $categories) : [];
        $categorySlug = end($segments);

        $category = ProductCategory::active()->where('slug', $categorySlug)->first();

        $products = $category->products()->paginate(12);
        $products = $this->cart->attachCartQtyToProducts($products);
        $products = $this->wishlist->attachWishlistFlag($products);

        $filters = $this->filterData();

        return view("frontend.product-category", compact('category', 'filters', 'segments'));

        return view('frontend.shop', compact('products', 'category', 'segments', 'filters'));
    }

    private function filterData()
    {
        $minPrice = Product::query()
            ->whereNotNull('regular_price')
            ->select(DB::raw('MIN(COALESCE(sale_price, regular_price)) as min_price'))
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->value('min_price') ?? 0;

        $maxPrice = Product::query()
            ->whereNotNull('regular_price')
            ->select(DB::raw('MAX(COALESCE(sale_price, regular_price)) as max_price'))
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->value('max_price') ?? 0;
        //dd($minPrice, $maxPrice);

        $filters = [
            'price' => [
                'min' => $minPrice ?? 0,
                'max' => $maxPrice ?? 0,
            ],

            'categories' => ProductCategory::active()
                ->whereNull('parent_id')
                ->withCount('products')
                ->with([
                    'children' => function ($q) {
                        $q->withCount('products')->where('status', 1);
                    }
                ])
                ->get()

                ->toArray(),

            'special' => Product::where('is_special', 1)->latest()->first(),
        ];

        return $filters;
    }

    // --- Product Details ---
    public function productDetails($slug)
    {

        $product = Product::active()->with(['categories', 'tags', 'galleries', 'variants.values.attribute', 'attributes.values', 'faqs'])->where('slug', $slug)->firstOrFail();


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



    // --- Blog List (Category/Sub-category) ---
    public function blogList($categories = null)
    {
        $segments = $categories ? explode('/', $categories) : [];
        $categorySlug = end($segments);

        $category = BlogCategory::where('slug', $categorySlug)->first();
        $posts = $category
            ? $category->posts()->paginate(10)
            : Post::paginate(10); // fallback to all posts

        return view('frontend.blog.list', compact('posts', 'category', 'segments'));
    }

    // --- Blog Details ---
    public function blogDetails($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        return view('frontend.blog.details', compact('post'));
    }


    public function sitemapXML()
    {


        // Pages (with children)
        $pages = Page::active()
            ->whereNotIn('id', [1])
            // ->where('domain_id', $domain->id)
            ->with('children')
            ->orderBy('title')
            ->get();

        // Categories (with multi-level children)
        $categories = BlogCategory::active()
            ->with('children.children')
            // ->where('domain_id', $domain->id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        // Articles
        $articles = BlogPost::active()
            //->where('domain_id', $domain->id)
            ->orderBy('title')
            ->get();

        $xml = $this->generateXml($pages, $categories, $articles);

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    private function generateXml($pages, $categories, $articles)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset 
                xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        // ==========================
        // PAGES + CHILDREN
        // ==========================
        foreach ($pages as $page) {
            $xml .= $this->urlTag(
                route('page', $page->slug),
                $page->updated_at,
                'monthly',
                '0.7'
            );

            if ($page->children) {
                foreach ($page->children as $child) {
                    $xml .= $this->urlTag(
                        route('page', $child->slug),
                        $child->updated_at,
                        'monthly',
                        '0.6'
                    );
                }
            }
        }

        // ==========================
        // CATEGORIES + CHILDREN + SUB-CHILDREN
        // ==========================
        foreach ($categories as $category) {
            $xml .= $this->urlTag(
                route('category', $category->full_slug ?? $category->slug),
                $category->updated_at,
                'weekly',
                '0.6'
            );

            foreach ($category->children ?? [] as $child) {
                $xml .= $this->urlTag(
                    route('category', $child->full_slug ?? $child->slug),
                    $child->updated_at,
                    'weekly',
                    '0.55'
                );

                foreach ($child->children ?? [] as $sub) {
                    $xml .= $this->urlTag(
                        route('category', $sub->full_slug ?? $sub->slug),
                        $sub->updated_at,
                        'weekly',
                        '0.50'
                    );
                }
            }
        }

        // ==========================
        // ARTICLES
        // ==========================
        foreach ($articles as $article) {
            $xml .= $this->urlTag(
                route('article', $article->slug),
                $article->updated_at,
                'daily',
                '0.8'
            );
        }

        $xml .= '</urlset>';

        return $xml;
    }
    private function urlTag($loc, $lastmod, $freq, $priority)
    {
        return "
        <url>
            <loc>{$loc}</loc>
            " . ($lastmod ? "<lastmod>{$lastmod->toAtomString()}</lastmod>" : "") . "
            <changefreq>{$freq}</changefreq>
            <priority>{$priority}</priority>
        </url>
    ";
    }


    public function contactFormSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email:rfc,dns',
            // 'subject' => 'required',
            'message' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
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
                'message' => 'Thanks for reaching out! We’ll get back to you soon.',
                'redirect_url' => route('page', 'thank-you'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function newsletterSubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns|unique:newsletters,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            Newsletter::create($request->only('email'));
            // Send confirmation email
            Mail::send('emails.newsletter', ['email' => $request->email], function ($mail) use ($request) {
                $mail->to($request->email)
                    ->subject('Thanks for Subscribing to Our Newsletter');
            });

            // Send email to admin
            // Mail::send('emails.newsletter_admin', [
            //     'email' => $request->email
            // ], function ($mail) {
            //     $mail->to('admin@example.com')
            //         ->subject('New Newsletter Subscriber');
            // });
            return response()->json([
                'status' => true,
                'message' => 'Subscription successful! You’ll start receiving updates soon.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function load(Request $request)
    {

        $query = Product::query()->stock()->active();

        // Category filter (SAFE)
        // if (is_array($request->categories) && count($request->categories) > 0) {
        //     $query->whereHas('categories', function ($q) use ($request) {
        //         $q->whereIn('product_categories.id', $request->categories);
        //     });
        // }
        if (!empty($request->categories) && is_array($request->categories) && count($request->categories) > 0) {

            $query->whereHas('categories', function ($q) use ($request) {
                $q->whereIn('product_categories.id', $request->categories);
            });
        }

        // Price
        $minPrice = $request->min_price !== '' ? (float) $request->min_price : null;
        $maxPrice = $request->max_price !== '' ? (float) $request->max_price : null;

        if ($minPrice !== null) {
            $query->where(function ($q) use ($minPrice) {
                $q->where('sale_price', '>=', $minPrice)
                    ->orWhere('sale_price', null)
                    ->where('regular_price', '>=', $minPrice);
            });
        }

        if ($maxPrice !== null) {
            $query->where(function ($q) use ($maxPrice) {
                $q->where('sale_price', '<=', $maxPrice)
                    ->orWhere('sale_price', null)
                    ->where('regular_price', '<=', $maxPrice);
            });
        }

        // Sorting
        switch ($request->sort) {
            case 'popular':
                $query->orderBy('views', 'desc'); // or 'rating', 'sold_count' etc.
                break;

            case 'name-asc':
                $query->orderBy('title', 'asc');
                break;

            case 'name-desc':
                $query->orderBy('title', 'desc');
                break;

            case 'price-low':
                // Use COALESCE to consider sale_price if available
                $query->orderByRaw('COALESCE(sale_price, regular_price) ASC');
                break;

            case 'price-high':
                $query->orderByRaw('COALESCE(sale_price, regular_price) DESC');
                break;

            default:
                $query->latest(); // default sorting by created_at
                break;
        }



        $products = $query->paginate(12, ['*'], 'page', $request->page);
        //dd($products);
        // $products = Product::active()->stock()->paginate(12);
        //  $products = $this->cart->attachCartQtyToProducts($products);
        // $products = $this->wishlist->attachWishlistFlag($products);
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
            'values' => 'required|array'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Find variant matching selected attribute values
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
            'sku' => $variant->sku
        ]);
    }

}
