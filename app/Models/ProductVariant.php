<?php
// app/Models/ProductVariant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'regular_price',
        'sale_price',
        'stock',
        'image',
        'image_alt',
        'status'
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Product relationship
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Attribute values relationship
     */
    public function values(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_variant_values',
            'variant_id',
            'attribute_value_id'
        )->withTimestamps();
    }

    /**
     * Alternative name for values (for consistency with cart code)
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->values();
    }

    /**
     * Get formatted attribute names (e.g., "Color: Red | Size: Large")
     */
    public function formattedAttributes(): string
    {
        return $this->values
            ->map(fn($v) => $v->attribute->name . ': ' . $v->name)
            ->join(' | ');
    }

    /**
     * Get variant display name (e.g., "Red - Large")
     */
    public function getNameAttribute(): string
    {
        if ($this->values->isEmpty()) {
            return 'Default Variant';
        }

        return $this->values->pluck('name')->join(' - ');
    }

    /**
     * Get selling price (sale price or regular price)
     */
    public function getSellingPriceAttribute(): float
    {
        return $this->sale_price ?? $this->regular_price ?? 0;
    }

    /**
     * Get variant image URL
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        // Fallback to product image
        return $this->product->image_url ?? asset('frontend/images/product.webp');
    }

    /**
     * Check if variant is in stock
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Check if variant is available (active and in stock)
     */
    public function isAvailable(): bool
    {
        return $this->status && $this->isInStock();
    }

    /**
     * Calculate discount percentage
     */
    public function discountPercentage(): int
    {
        if (!$this->sale_price || $this->regular_price <= 0) {
            return 0;
        }

        return (int) round((($this->regular_price - $this->sale_price) / $this->regular_price) * 100);
    }

    /**
     * Decrease stock
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock < $quantity) {
            return false;
        }

        $this->decrement('stock', $quantity);
        return true;
    }

    /**
     * Increase stock
     */
    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    /**
     * Scope: Only active variants
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Only variants in stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope: Available variants (active and in stock)
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', true)->where('stock', '>', 0);
    }
}