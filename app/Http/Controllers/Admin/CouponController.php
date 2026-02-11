<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductCategory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Coupon::with(['rules.product', 'rules.category', 'actions.product'])
                ->orderByDesc('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('value', function ($coupon) {
                    if ($coupon->type === 'percentage') {
                        return $coupon->value . '%';
                    } elseif ($coupon->type === 'fixed') {
                        return currencyformat($coupon->value);
                    }
                    return $coupon->value;
                })
                ->addColumn('starts_at', fn($coupon) => $coupon->starts_at ? $coupon->starts_at->format('d M, Y H:i') : '')
                ->addColumn('expires_at', fn($coupon) => $coupon->expires_at ? $coupon->expires_at->format('d M, Y H:i') : '')
                ->addColumn('status', fn($coupon) => status_badge($coupon->status))
                ->addColumn('actions', function ($coupon) {
                    $edit = '<a href="' . route('admin.coupons.edit', $coupon->id) . '" class="btn btn-sm btn-primary me-1"><i class="bi bi-pencil-fill"></i></a>';
                    $delete = '<form method="POST" action="' . route('admin.coupons.destroy', $coupon->id) . '" style="display:inline;">' .
                        csrf_field() . method_field('DELETE') .
                        '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')"><i class="bi bi-trash-fill"></i></button></form>';
                    return $edit . $delete;
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('admin.ecommerce.coupons.index');
    }

    public function create()
    {
        return view('admin.ecommerce.coupons.form', [
            'products' => Product::all(),
            'categories' => ProductCategory::all(),
            'coupon' => new Coupon(),
        ]);
    }

    public function store(CouponRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $coupon = Coupon::create($data);

            // Save rules
            if (!empty($request->rules)) {
                foreach ($request->rules as $rule) {
                    $coupon->rules()->create([
                        'condition' => $rule['condition'] ?? null,
                        'product_id' => $rule['product_id'] ?? null,
                        'category_id' => $rule['category_id'] ?? null,
                        'min_value' => $rule['min_value'] ?? null,
                        'min_qty' => $rule['min_qty'] ?? null,
                    ]);
                }
            }

            // Save actions (including BOGO)
            if (!empty($request->actions)) {
                foreach ($request->actions as $action) {
                    $coupon->actions()->create([
                        'action' => $action['action'] ?? null,
                        'product_id' => $action['product_id'] ?? null,
                        'value' => $action['value'] ?? null,
                        'quantity' => $action['quantity'] ?? null,
                        'buy_qty' => $action['buy_qty'] ?? null,   // BOGO Buy Qty
                        'get_qty' => $action['get_qty'] ?? null,   // BOGO Get Qty
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Coupon creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Failed to create coupon. Please try again.');
        }
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.ecommerce.coupons.form', [
            'coupon' => $coupon->load('rules', 'actions'),
            'products' => Product::all(),
            'categories' => ProductCategory::all(),
        ]);
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $coupon->update($data);

            // Sync rules
            $coupon->rules()->delete();
            if (!empty($request->rules)) {
                foreach ($request->rules as $rule) {
                    $coupon->rules()->create([
                        'condition' => $rule['condition'] ?? null,
                        'product_id' => $rule['product_id'] ?? null,
                        'category_id' => $rule['category_id'] ?? null,
                        'min_value' => $rule['min_value'] ?? null,
                        'min_qty' => $rule['min_qty'] ?? null,
                    ]);
                }
            }

            // Sync actions (including BOGO)
            $coupon->actions()->delete();
            if (!empty($request->actions)) {
                foreach ($request->actions as $action) {
                    $coupon->actions()->create([
                        'action' => $action['action'] ?? null,
                        'product_id' => $action['product_id'] ?? null,
                        'value' => $action['value'] ?? null,
                        'quantity' => $action['quantity'] ?? null,
                        'buy_qty' => $action['buy_qty'] ?? null,   // BOGO Buy Qty
                        'get_qty' => $action['get_qty'] ?? null,   // BOGO Get Qty
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Coupon update failed', [
                'coupon_id' => $coupon->id,
                'error' => $e->getMessage(),
            ]);
            return back()->withInput()->with('error', 'Failed to update coupon. Please try again.');
        }
    }

    public function destroy(Coupon $coupon)
    {
        DB::beginTransaction();
        try {
            $coupon->delete();
            DB::commit();
            return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Coupon deletion failed', [
                'coupon_id' => $coupon->id,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to delete coupon. Please try again.');
        }
    }
}
