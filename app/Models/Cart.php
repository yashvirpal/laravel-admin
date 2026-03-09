<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'subtotal',
        'discount_total',
        'tax_rate',
        'tax_total',
        'shipping_method',
        'shipping_label',
        'shipping_total',
        'grand_total',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    protected $attributes = [
        'subtotal' => 0,
        'discount_total' => 0,
        'tax_rate' => 0,
        'tax_total' => 0,
        'shipping_total' => 0,
        'grand_total' => 0,
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'cart_coupons')
            ->withPivot('discount_amount')  // ← CRITICAL: This tells Laravel to include discount_amount
            ->withTimestamps();
    }
    /**
     * Calculate and update cart totals
     */
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum(fn($item) => $item->price * $item->quantity);

        // Tax calculation (after discount)
        $taxableAmount = $this->subtotal - $this->discount_total;
        $this->tax_total = ($taxableAmount * $this->tax_rate) / 100;

        // Grand total
        $this->grand_total = $this->subtotal - $this->discount_total + $this->tax_total + $this->shipping_total;

        $this->save();
    }
}