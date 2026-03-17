<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'email',
        'rating',
        'review'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
