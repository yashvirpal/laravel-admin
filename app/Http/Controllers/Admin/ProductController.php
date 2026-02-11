<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ImageUploadService;
use function Pest\Laravel\json;

class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with(['categories', 'tags', 'author'])->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('categories', fn($p) => $p->categories->pluck('title')->implode(', '))
                ->addColumn('tags', fn($p) => $p->tags->pluck('title')->implode(', '))
                // ->addColumn('price', fn($p) => currencyformat($p->regular_price))
                ->addColumn('price', function ($p) {
                    if ($p->variants->count()) {
                        // Variant Product → Price Range (sale or regular)
                        $prices = $p->variants->map(function ($v) {
                            return $v->sale_price && $v->sale_price < $v->regular_price
                                ? $v->sale_price
                                : $v->regular_price;
                        });

                        $min = $prices->min();
                        $max = $prices->max();

                        return $min == $max
                            ? currencyformat($min)
                            : currencyformat($min) . ' - ' . currencyformat($max);

                    } else {
                        // Simple Product → Sale Price logic
                        $price = ($p->sale_price && $p->sale_price < $p->regular_price)
                            ? $p->sale_price
                            : $p->regular_price;

                        return currencyformat($price);
                    }
                })

                ->addColumn('status', fn($p) => status_badge($p->status))
                ->addColumn('type', fn($p) => $p->has_variants ? 'Variants' : 'Simple')
                ->addColumn('actions', function ($p) {
                    $edit = '<a href="' . route('admin.products.edit', $p->id) . '" class="btn btn-sm btn-primary me-1" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                             </a>';
                    $delete = '<form method="POST" action="' . route('admin.products.destroy', $p->id) . '" style="display:inline;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')" title="Delete">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                              </form>';
                    return $edit . $delete;
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('admin.ecommerce.products.index');
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        //dd( ProductAttribute::with('values')->get());
        $categories = ProductCategory::where('status', 1)->whereNull('parent_id')->with('children')->get();
        $tags = ProductTag::where('status', 1)->pluck('title', 'id');

        return view('admin.ecommerce.products.form', [
            'product' => null,
            'categories' => $categories,
            'tags' => $tags,
            'attributes' => ProductAttribute::with('values')->get(),
        ]);
    }

    /**
     * Store a newly created product in storage
     */
    public function store(ProductRequest $request)
    {

        DB::beginTransaction();

        try {
            $data = $request->validated();
            // dd($request->all(), "store");
            $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
            $data['author_id'] = Auth::id();

            // Upload images
            if ($request->hasFile('banner')) {
                $banner = $this->imageService->upload($request->file('banner'), 'banner');
                $data['banner'] = $banner['name'];
            }

            if ($request->hasFile('image')) {
                $image = $this->imageService->upload($request->file('image'), 'product');
                $data['image'] = $image['name'];
            }

            if ($request->hasFile('seo_image')) {
                $seo = $this->imageService->upload($request->file('seo_image'), 'seo');
                $data['seo_image'] = $seo['name'];
            }

            // Create product
            $product = Product::create($data);

            // Sync categories and tags
            $product->categories()->sync($request->product_category_ids ?? []);
            $product->tags()->sync($request->product_tag_ids ?? []);

            // Gallery upload
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $index => $file) {
                    $uploaded = $this->imageService->upload($file, 'product_gallery');
                    $product->galleries()->create([
                        'image' => $uploaded['name'],
                        'alt' => $request->input('gallery_alt.' . $index, ''),
                        'sort_order' => $index,
                        'is_default' => $index === 0,
                    ]);
                }
            }

            $this->processProductVariants($product, $request);





            DB::commit();
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully.',
                    'redirect_url' => route('admin.products.index')
                ]);
            }
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Product store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return back()->withInput()->with('error', 'Something went wrong while creating the product.');
        }
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $product->load(['galleries', 'categories', 'tags']);

        $categories = ProductCategory::where('status', 1)->whereNull('parent_id')->with('children')->get();
        $tags = ProductTag::where('status', 1)->pluck('title', 'id');
        $attributes = ProductAttribute::with('values')->get();
        return view('admin.ecommerce.products.form', compact('product', 'categories', 'tags', 'attributes'));
    }

    /**
     * Update the specified product in storage
     */
    public function update(ProductRequest $request, Product $product)
    {
       
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // return response()->json(['message' => 'Update function called'],401);
            $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

            // Remove selected gallery images
            if ($request->filled('remove_gallery')) {
                foreach ($request->remove_gallery as $imageId) {
                    $image = $product->galleries()->find($imageId);
                    if ($image) {
                        $this->imageService->delete($image->image, 'product_gallery');
                        $image->delete();
                    }
                }
            }

            // Upload & replace individual images
            if ($request->hasFile('banner')) {
                $this->imageService->delete($product->banner ?? null, 'banner');
                $banner = $this->imageService->upload($request->file('banner'), 'banner');
                $data['banner'] = $banner['name'];
            }

            if ($request->hasFile('image')) {
                $this->imageService->delete($product->image ?? null, 'product');
                $image = $this->imageService->upload($request->file('image'), 'product');
                $data['image'] = $image['name'];
            }

            if ($request->hasFile('seo_image')) {
                $this->imageService->delete($product->seo_image ?? null, 'seo');
                $seo = $this->imageService->upload($request->file('seo_image'), 'seo');
                $data['seo_image'] = $seo['name'];
            }

            // Update main product data
            $product->update($data);

            // Sync relationships
            $product->categories()->sync($request->product_category_ids ?? []);
            $product->tags()->sync($request->product_tag_ids ?? []);

            // Handle new gallery uploads
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $index => $file) {
                    $uploaded = $this->imageService->upload($file, 'product_gallery');
                    $product->galleries()->create([
                        'image' => $uploaded['name'],
                        'alt' => $request->input('gallery_alt.' . $index, ''),
                        'sort_order' => $index,
                        'is_default' => $index === 0,
                    ]);
                }
            }

            // Update default gallery image if chosen
            if ($request->filled('default_gallery')) {
                $product->galleries()->update(['is_default' => false]);
                $product->galleries()->where('id', $request->default_gallery)->update(['is_default' => true]);
            }
            // Sync variants if applicable
            $this->processProductVariants($product, $request);

            DB::commit();
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product updated successfully.',
                    'redirect_url' => route('admin.products.index')
                ]);
            }

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Product update failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return back()->withInput()->with('error', 'Something went wrong while updating the product.');
        }
    }

    /**
     * Remove the specified product from storage
     */
    public function destroy(Product $product)
    {
        try {
            foreach ($product->galleries as $img) {
                $this->imageService->delete($img->image, 'product_gallery');
            }

            $this->imageService->delete($product->banner ?? null, 'banner');
            $this->imageService->delete($product->image ?? null, 'product');
            $this->imageService->delete($product->seo_image ?? null, 'seo');

            $product->attributes()->sync([]);
            foreach ($product->variants as $variant) {
                if ($variant->image) {
                    $this->imageService->delete($variant->image, 'product_variant');
                }
                $variant->values()->detach();
                $variant->delete();
            }

            $product->delete();

            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Product delete failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('error', 'Unable to delete product.');
        }
    }

    protected function processProductVariants($product, Request $request)
    {

        if ($request->boolean('has_variants')) {
            Log::info('Processing variants for product: ' . $product->id);
            // 1) Sync Attributes
            if ($request->filled('attributes')) {
                $attributeData = $request->input('attributes');
                $attributeIds = array_keys($attributeData);
                $product->attributes()->sync($attributeIds);
            }

            // 2) Handle Variant Records
            if ($request->filled('variants')) {
                $submittedVariantIds = [];

                foreach ($request->variants as $id => $variantData) {
                    $isNew = str_starts_with($id, 'new_');

                    // Create or Update variant
                    $variantModel = $product->variants()->updateOrCreate(
                        ['id' => $isNew ? null : $id],
                        [
                            'sku' => $variantData['sku'] ?? null,
                            'regular_price' => $variantData['regular_price'] ?? 0,
                            'sale_price' => $variantData['sale_price'] ?? null,
                            'stock' => $variantData['stock'] ?? 0,
                            'status' => true,
                        ]
                    );

                    $submittedVariantIds[] = $variantModel->id;

                    // 3) Variant Image Upload
                    if ($request->hasFile("variants.$id.image")) {
                        $uploaded = $this->imageService->upload(
                            $request->file("variants.$id.image"),
                            'product_variant'
                        );
                        $variantModel->update(['image' => $uploaded['name']]);
                    }

                    // 4) Assign attribute value pivots
                    if (!empty($variantData['attribute_values'])) {
                        $valueIds = explode(',', $variantData['attribute_values']);
                        $variantModel->values()->sync($valueIds);
                    }
                }

                // 5) Cleanup: Delete variants removed from the UI
                //  $product->variants()->whereNotIn('id', $submittedVariantIds)->delete();

                $removedVariants = $product->variants()->whereNotIn('id', $submittedVariantIds)->get();
                // Delete images & variants
                foreach ($removedVariants as $variant) {
                    // Delete stored variant image if exists
                    if ($variant->image) {
                        $this->imageService->delete($variant->image, 'product_variant');
                    }

                    // Detach attribute pivots
                    $variant->values()->detach();

                    // Delete the variant
                    $variant->delete();
                }
            }
        } else {
            /**
             * SIMPLE PRODUCT LOGIC
             */
            $product->update([
                'sku' => $request->sku ?? null,
                'regular_price' => $request->regular_price ?? 0,
                'sale_price' => $request->sale_price ?? null,
                'stock' => $request->stock ?? 0,
            ]);
            Log::info('Updating simple product: ' . $product->id);
            Log::info('Updating simple product: ' . $product);
            $product->attributes()->sync([]);
            foreach ($product->variants as $variant) {
                // Optional: If variants have images, delete them from storage here
                // Storage::delete('public/variants/' . $variant->image);

                // This will also delete pivot entries if you have 'onDelete(cascade)' in your migration
                $variant->values()->detach();
                $variant->delete();
            }
        }

        /**
         * ==========================================================
         * VARIANT / ATTRIBUTE PROCESSING STARTS HERE
         * ==========================================================
         */
        // if ($request->boolean('has_variants')) {

        //     // 1) Sync Attributes used for this product
        //     // Your HTML name is attributes[attr_id][]
        //     // if ($request->filled('attributes')) {
        //     //    // $attributeIds = array_keys($request->attributes);
        //     //     $attributeIds = array_keys($request->input('attributes', []));
        //     //     $product->attributes()->sync($attributeIds);
        //     // }
        //     if ($request->filled('attributes')) {
        //         // Use input() to ensure we get an array, not a ParameterBag
        //         $attributeData = $request->input('attributes');
        //         $attributeIds = array_keys($attributeData);

        //         $product->attributes()->sync($attributeIds);
        //     }

        //     // 2) Handle Variant Records (Create or Update)
        //     if ($request->filled('variants')) {
        //         $submittedVariantIds = [];

        //         foreach ($request->variants as $id => $variantData) {
        //             // Check if it's an existing ID or a "new_..." temporary ID
        //             $isNew = str_starts_with($id, 'new_');

        //             // Use updateOrCreate to handle both Edit and Create
        //             // If it's new, we pass null as ID so it creates a new record
        //             $variantModel = $product->variants()->updateOrCreate(
        //                 ['id' => $isNew ? null : $id],
        //                 [
        //                     'sku' => $variantData['sku'] ?? null,
        //                     'regular_price' => $variantData['regular_price'] ?? 0,
        //                     'sale_price' => $variantData['sale_price'] ?? null,
        //                     'stock' => $variantData['stock'] ?? 0,
        //                     'status' => true,
        //                 ]
        //             );

        //             $submittedVariantIds[] = $variantModel->id;

        //             // 3) Variant Image Upload
        //             if ($request->hasFile("variants.$id.image")) {
        //                 $uploaded = $this->imageService->upload(
        //                     $request->file("variants.$id.image"),
        //                     'product_variant'
        //                 );
        //                 $variantModel->update(['image' => $uploaded['name']]);
        //             }

        //             // 4) Assign attribute value pivots
        //             // Your hidden input name is: variants[tempId][attribute_values]
        //             if (!empty($variantData['attribute_values'])) {
        //                 // attribute_values is a comma-separated string like "1,5"
        //                 $valueIds = explode(',', $variantData['attribute_values']);
        //                 $variantModel->values()->sync($valueIds);
        //             }
        //         }

        //         // 5) OPTIONAL: Delete variants that were removed from the table
        //         $product->variants()->whereNotIn('id', $submittedVariantIds)->delete();
        //     }

        // } else {
        //     /**
        //      * SIMPLE PRODUCT (NO VARIANTS)
        //      * We use updateOrCreate to ensure we don't keep creating new simple variants on every edit
        //      */
        //     $product->updateOrCreate(
        //         ['product_id' => $product->id], // Assuming a simple product has only one variant row
        //         [
        //             'sku' => $request->sku ?? null,
        //             'regular_price' => $request->regular_price ?? 0,
        //             'sale_price' => $request->sale_price ?? null,
        //             'stock' => $request->stock ?? 0,
        //         ]
        //     );
        // }
    }


}
