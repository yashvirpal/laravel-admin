<?php

namespace App\Listeners;

use App\Mail\ResetPasswordMail;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SendResetPasswordMail
{
    public function handle(PasswordResetLinkSent $event): void
    {
        // Get the hashed token from DB
        $record = DB::table('password_reset_tokens')
            ->where('email', $event->user->email)
            ->first();

        if (!$record)
            return;

        // ❌ $record->token is hashed — can't use it directly
        // ✅ Create a fresh token instead
        $token = app('auth.password.broker')->createToken($event->user);

        $resetUrl = url(route('password.reset', [
            'token' => $token,
            // 'email' => $event->user->email,
            'ue' => encrypt($event->user->email),
        ], false));

        Mail::to($event->user->email)
            ->send(new ResetPasswordMail($resetUrl, $event->user->name));
    }
}