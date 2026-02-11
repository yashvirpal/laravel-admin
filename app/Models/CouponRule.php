<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'condition',
        'product_id',
        'category_id',
        'min_value',
        'min_qty',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
