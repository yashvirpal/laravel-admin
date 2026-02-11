<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductAttributeRequest;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class ProductAttributeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductAttribute::orderByDesc('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status', fn($attr) => status_badge($attr->status))
                ->addColumn('actions', function ($attr) {
                    $editUrl = route('admin.product-attributes.edit', $attr->id);
                    $deleteUrl = route('admin.product-attributes.destroy', $attr->id);
                    $showUrl = route('admin.product-attributes.show', $attr->id);

                    return '
                     <a href="' . $showUrl . '" class="btn btn-sm btn-info me-1">
                            <i class="bi bi-eye"></i> View
                        </a>
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

        return view('admin.ecommerce.attributes.index');
    }

    public function create()
    {
        $attribute = new ProductAttribute();
        return view('admin.ecommerce.attributes.form', compact('attribute'));
    }

    public function store(ProductAttributeRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['slug'] = Str::slug($data['slug']) ?: Str::slug($data['name']);

            ProductAttribute::create($data);

            DB::commit();
            return redirect()->route('admin.product-attributes.index')->with('success', 'Attribute created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProductAttribute Store Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating attribute.');
        }
    }
    public function show(ProductAttribute $productAttribute)
    {
        $productAttribute->load('values'); // assuming relation name = values
        return view('admin.ecommerce.attributes.show', compact('productAttribute'));
    }
    public function edit(ProductAttribute $product_attribute)
    {
        return view('admin.ecommerce.attributes.form', ['attribute' => $product_attribute]);
    }

    public function update(ProductAttributeRequest $request, ProductAttribute $product_attribute)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['slug'] = Str::slug($data['slug']) ?: Str::slug($data['name']);

            $product_attribute->update($data);

            DB::commit();
            return redirect()->route('admin.product-attributes.index')->with('success', 'Attribute updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProductAttribute Update Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating attribute.');
        }
    }

    public function destroy(ProductAttribute $product_attribute)
    {
        DB::beginTransaction();

        try {
            $product_attribute->delete();

            DB::commit();
            return redirect()->route('admin.product-attributes.index')->with('success', 'Attribute deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ProductAttribute Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Error deleting attribute.');
        }
    }
}
