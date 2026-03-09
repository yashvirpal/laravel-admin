<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #DC2626; padding: 32px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 24px; }
        .body { padding: 32px; color: #333; line-height: 1.6; }
        .btn { display: inline-block; margin-top: 24px; padding: 12px 28px; background: #DC2626; color: #fff !important; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .expiry { margin-top: 20px; padding: 12px 16px; background: #FEF2F2; border-left: 4px solid #DC2626; border-radius: 4px; font-size: 14px; color: #991B1B; }
        .url-box { margin-top: 16px; padding: 12px; background: #f4f4f4; border-radius: 4px; font-size: 12px; word-break: break-all; color: #666; }
        .footer { padding: 20px 32px; text-align: center; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>
        <div class="body">
            <p>Hi {{ $userName }},</p>
            <p>We received a request to reset the password for your account. Click the button below to set a new password.</p>

            <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>

            <div class="expiry">
                ⏱ This link will expire in <strong>60 minutes</strong>.
            </div>

            <p style="margin-top: 24px;">If the button doesn't work, copy and paste this URL into your browser:</p>
            <div class="url-box">{{ $resetUrl }}</div>

            <p style="margin-top: 24px; color: #999; font-size: 13px;">
                If you did not request a password reset, no action is needed. Your password will remain unchanged.
            </p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>