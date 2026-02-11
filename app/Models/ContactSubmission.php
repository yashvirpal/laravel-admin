<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'subject',
        'message',
        'attachment',
        'status',
        'custom_field',
        'referrer_url',
        'user_agent',
        'ip_address',
    ];
}
