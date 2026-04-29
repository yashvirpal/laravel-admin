<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkEnquiry extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'message',
        'products',
        'quantity',
        'referrer_url',
        'ip_address',
        'user_agent',
        'browser',
        'platform',
        'device',
    ];

    protected $casts = [
        'products' => 'array',
    ];
}
