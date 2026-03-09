<?php
// app/Models/OrderCoupon.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderCoupon extends Model
{
    protected $fillable = [
        'order_id',
        'coupon_id',
        'code',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}