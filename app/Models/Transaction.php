<?php
// app/Models/Transaction.php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'transaction_id',
        'amount',
        'payment_method',
        'status',
        'response_data',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'response_data' => 'array',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Order::class,
            'id',
            'id',
            'order_id',
            'user_id'
        );
    }

    // Scopes
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'success' => '<span class="badge bg-success">Success</span>',
            'failed' => '<span class="badge bg-danger">Failed</span>',
            'refunded' => '<span class="badge bg-info">Refunded</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'cash' => 'Cash',
            'card' => 'Card',
            'upi' => 'UPI',
            'wallet' => 'Wallet',
            'cod' => 'Cash on Delivery',
        ];

        return $labels[$this->payment_method] ?? ucfirst($this->payment_method);
    }
}