<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Subscription Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            padding: 0;
            margin: 0;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        h2 {
            color: #111827;
            margin-bottom: 20px;
        }

        p {
            line-height: 1.6;
            color: #374151;
            font-size: 15px;
        }

        .btn {
            display: inline-block;
            background: #2563eb;
            color: #ffffff !important;
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 15px;
            font-weight: bold;
        }

        .footer {
            margin-top: 25px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🎉 You're now subscribed!</h2>

    <p>Hi,</p>

    <p>Thank you for subscribing to <strong>{{ config('app.name') }}</strong>.  
    You will now receive regular crypto news, updates, and alerts.</p>

    <p>If you ever wish to unsubscribe, you can do so anytime.</p>

    <a class="btn" href="{{ url('/') }}">Visit Website</a>

    <p class="footer">
        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </p>
</div>

</body>
</html>
