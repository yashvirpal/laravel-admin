<?php
// app/Models/Address.php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'first_name',
        'last_name',
        'company',
        'address_line1',
        'address_line2',
        'phone',
        'city',
        'state',
        'country',
        'zip',
        'status',
        'is_default',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_default' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function billingOrders()
    {
        return $this->hasMany(Order::class, 'billing_address_id');
    }

    public function shippingOrders()
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    // Scopes
    public function scopeBilling($query)
    {
        return $query->where('type', 'billing');
    }

    public function scopeShipping($query)
    {
        return $query->where('type', 'shipping');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getFormattedAddressAttribute()
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    // Methods
    public function makeDefault()
    {
        // Remove default from other addresses of same type
        static::where('user_id', $this->user_id)
            ->where('type', $this->type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);

        return $this;
    }

    // public function toArray()
    // {
    //     return [
    //         'type'=>$this->type,
    //         'first_name' => $this->first_name,
    //         'last_name' => $this->last_name,
    //         'company' => $this->company,
    //         'address_line1' => $this->address_line1,
    //         'address_line2' => $this->address_line2,
    //         'phone' => $this->phone,
    //         'city' => $this->city,
    //         'state' => $this->state,
    //         'country' => $this->country,
    //         'postal_code' => $this->postal_code,
    //     ];
    // }
}