<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon')?->id;

        return [
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:coupons,code,' . $couponId,
            'status' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:0',

            'rules' => 'nullable|array',
            'rules.*.condition' => 'required|string|in:product,category,cart_subtotal,cart_quantity',
            'rules.*.product_id' => 'nullable|exists:products,id',
            'rules.*.category_id' => 'nullable|exists:product_categories,id',
            'rules.*.min_value' => 'nullable|numeric|min:0',
            'rules.*.min_qty' => 'nullable|integer|min:0',

            'actions' => 'nullable|array',
            'actions.*.action' => 'required|string|in:fixed_discount,percentage_discount,free_product,discount_product,bogo',
            'actions.*.product_id' => 'nullable|exists:products,id',
            'actions.*.value' => 'nullable|numeric|min:0',
            'actions.*.quantity' => 'nullable|integer|min:1',
            'actions.*.buy_qty' => 'nullable|integer|min:1',
            'actions.*.get_qty' => 'nullable|integer|min:1',
        ];
    }
}