<?php
// app/Models/Order.php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'billing_address_id',
        'shipping_address_id',
        'shipping_address',
        'billing_address',
        'subtotal',
        'discount_total',
        'tax_total',
        'shipping_total',
        'total',
        'shipping_method',
        'payment_method',
        'payment_status',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'total' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupons()
    {
        return $this->hasMany(OrderCoupon::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function latestTransaction()
    {
        return $this->hasOne(Transaction::class)->latest();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'pending');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'processing' => '<span class="badge bg-info">Processing</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            'refunded' => '<span class="badge bg-secondary">Refunded</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'paid' => '<span class="badge bg-success">Paid</span>',
            'failed' => '<span class="badge bg-danger">Failed</span>',
            'refunded' => '<span class="badge bg-secondary">Refunded</span>',
        ];

        return $badges[$this->payment_status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    // Methods
    public static function generateOrderNumber()
    {
        do {
            $number = 'ORD-' . strtoupper(uniqid());
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function getTotalItems()
    {
        return $this->items()->sum('quantity');
    }

    public function canCancel()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canRefund()
    {
        return $this->status === 'completed' && $this->payment_status === 'paid';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }
}