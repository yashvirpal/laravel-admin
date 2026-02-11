<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    protected $fillable = [
        'title',
        'code',
        'status',
        'starts_at',
        'expires_at',
        'usage_limit',
        'used_count',
    ];

    protected $casts = [
        'status' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
    ];

    // Coupon rules (conditions)
    public function rules(): HasMany
    {
        return $this->hasMany(CouponRule::class);
    }

    // Coupon actions (discount/free product)
    public function actions(): HasMany
    {
        return $this->hasMany(CouponAction::class);
    }

    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Cart::class, 'cart_coupons')
            ->withPivot('discount_amount')
            ->withTimestamps();
    }

    // Check if coupon is valid now
    public function isActive(): bool
    {
        $now = now();

        if (!$this->status)
            return false;
        if ($this->starts_at && $this->starts_at > $now)
            return false;
        if ($this->expires_at && $this->expires_at < $now)
            return false;

        if ($this->usage_limit && $this->used_count >= $this->usage_limit)
            return false;

        return true;
    }
}
