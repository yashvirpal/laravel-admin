<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $fillable = ['name', 'slug', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];
    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }

    /**
     * Products that use this attribute
     */
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_product_attribute',
            'product_attribute_id',
            'product_id'
        )->withTimestamps();
    }
    /**
     * Scope: Only active attributes
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
