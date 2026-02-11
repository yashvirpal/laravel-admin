<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductAttributeValueRequest;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class ProductAttributeValueController extends Controller
{
    public function index(Request $request, ProductAttribute $product_attribute)
    {
        if ($request->ajax()) {
            $query = ProductAttributeValue::where('attribute_id', $product_attribute->id)->orderByDesc('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status', fn($val) => status_badge($val->status))
                ->addColumn('actions', function ($val) use ($product_attribute) {
                    $editUrl = route('admin.product-attribute-values.edit', [$product_attribute->id, $val->id]);
                    $deleteUrl = route('admin.product-attribute-values.destroy', [$product_attribute->id, $val->id]);

                    return '
                        <a href="' . $editUrl . '" class="btn btn-sm btn-primary me-1"><i class="bi bi-pencil-fill"></i></a>
                        <form method="POST" action="' . $deleteUrl . '" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('admin.ecommerce.attribute-values.index', compact('product_attribute'));
    }

    public function create(ProductAttribute $product_attribute)
    {
        $value = new ProductAttributeValue();
        return view('admin.ecommerce.attribute-values.form', compact('value', 'product_attribute'));
    }

    public function store(ProductAttributeValueRequest $request, ProductAttribute $product_attribute)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['attribute_id'] = $product_attribute->id;
            $data['slug'] = Str::slug($data['slug']) ?: Str::slug($data['name']);

            ProductAttributeValue::create($data);

            DB::commit();
            return redirect()->route('admin.product-attributes.show', $product_attribute->id)->with('success', 'Value created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProductAttributeValue Store Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating value.');
        }
    }

    public function edit(ProductAttribute $product_attribute, ProductAttributeValue $value)
    {

        return view('admin.ecommerce.attribute-values.form', [
            'product_attribute_value' => $value,
            'product_attribute' => $product_attribute
        ]);
    }

    public function update(ProductAttributeValueRequest $request, ProductAttribute $product_attribute, ProductAttributeValue $value)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['slug'] = Str::slug($data['slug']) ?: Str::slug($data['name']);

            $value->update($data);

            DB::commit();
            return redirect()->route('admin.product-attributes.show', $product_attribute->id)
                ->with('success', 'Value updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProductAttributeValue Update Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating value.');
        }
    }

    public function destroy(ProductAttribute $product_attribute, ProductAttributeValue $value)
    {
        DB::beginTransaction();

        try {
            $value->delete();
            DB::commit();

            return redirect()->route('admin.product-attributes.show', $product_attribute->id)
                ->with('success', 'Value deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProductAttributeValue Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Error deleting value.');
        }
    }
}
