<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Notification' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }

        .email-wrapper {
            max-width: 650px;
            margin: auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background: #111827;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .logo {
            margin-bottom: 12px;
        }

        .logo img {
            max-height: 50px;
        }

        .header-title {
            font-size: 22px;
            font-weight: bold;
        }

        .content {
            padding: 25px;
        }

        .footer {
            padding: 15px 25px;
            background: #f9fafb;
            font-size: 13px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">

        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}">
            </div>

            <div class="header-title">
                {{ $headerTitle ?? 'Notification' }}
            </div>
        </div>

        <div class="content">
            @yield('content')
        </div>

        <div class="footer">
            {{ $footerText ?? 'Notification' }} • © {{ date('Y') }} {{ config('app.name') }}
        </div>

    </div>
</body>
</html>