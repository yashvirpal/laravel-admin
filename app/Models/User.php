<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'country_code',
        'phone',
        'password',
        'profile_image',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    // 🔹 All addresses
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // 🔹 Multiple billing addresses
    public function billingAddresses()
    {
        return $this->hasMany(Address::class)->where('type', 'billing');
    }

    // 🔹 Multiple shipping addresses
    public function shippingAddresses()
    {
        return $this->hasMany(Address::class)->where('type', 'shipping');
    }

    public function defaultBillingAddress()
    {
        return $this->hasOne(Address::class)
            ->where('type', 'billing')
            ->where('is_default', true);
    }

    public function defaultShippingAddress()
    {
        return $this->hasOne(Address::class)
            ->where('type', 'shipping')
            ->where('is_default', true);
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    // Accessors
    public function getFullPhoneAttribute()
    {
        return $this->country_code ? "+{$this->country_code}{$this->phone}" : $this->phone;
    }

    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image
            ? asset('storage/users/' . $this->profile_image)
            : asset('images/default-avatar.png');
    }

    // Methods
    public function getTotalOrders()
    {
        return $this->orders()->count();
    }

    public function getTotalSpent()
    {
        return $this->orders()
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    public function getPendingOrders()
    {
        return $this->orders()
            ->whereIn('status', ['pending', 'processing'])
            ->count();
    }

    public function getInitialsAttribute()
    {
        $name = trim($this->name);
        $words = preg_split('/\s+/', $name);

        // If name has 3 or more words → take first letter of first 3 words
        if (count($words) >= 3) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1) . substr($words[2], 0, 1));
        }

        // If name has 2 words → take first letters of both
        if (count($words) == 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        // If name is single word → take first 2 letters
        return strtoupper(substr($words[0], 0, 2));
    }

    public function wishlists()
    {
        return $this->hasMany(\App\Models\Wishlist::class);
    }
    public function sendPasswordResetNotification($token): void
    {
        // ✅ Do nothing — our listener handles it
    }

}
