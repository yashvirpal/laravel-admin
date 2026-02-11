<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'name', 'slug', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];
    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_values', 'attribute_value_id', 'variant_id');
    }

    /**
     * Scope: Only active values
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get full display name (Attribute: Value)
     */
    public function getFullNameAttribute(): string
    {
        return $this->attribute->name . ': ' . $this->name;
    }
}
