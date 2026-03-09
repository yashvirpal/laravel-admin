<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #4F46E5; padding: 32px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 24px; }
        .body { padding: 32px; color: #333; line-height: 1.6; }
        .btn { display: inline-block; margin-top: 24px; padding: 12px 28px; background: #4F46E5; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .footer { padding: 20px 32px; text-align: center; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to {{ config('app.name') }}!</h1>
        </div>
        <div class="body">
            <p>Hi {{ $user->name }},</p>
            <p>Thanks for creating an account. We're excited to have you on board!</p>
            <p>Your account is now active and ready to use. Click the button below to go to your dashboard.</p>
            <a href="{{ route('profile.dashboard') }}" class="btn">Go to Dashboard</a>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>If you didn't create this account, please ignore this email.</p>
        </div>
    </div>
</body>
</html>