<?php
// app/Models/OrderItem.php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'variant_name',
        'sku',
        'quantity',
        'price',
        'subtotal',
        'custom_data',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'custom_data' => 'array',  // ✅ needed to store JSON
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // Accessors
    public function getFullProductNameAttribute()
    {
        return $this->variant_name 
            ? "{$this->product_name} - {$this->variant_name}" 
            : $this->product_name;
    }
}