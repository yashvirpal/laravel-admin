<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Notification' }}</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #374151;
        }

        .email-container {
            width: 100%;
            padding: 30px 15px;
            background-color: #f3f4f6;
        }

        .email-wrapper {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
        }

        .header {
            background: linear-gradient(135deg, #f5f5a8, #fff7cc);
            text-align: center;
            padding: 10px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .header {
            background: linear-gradient(135deg, #f5f5a8, #fff7cc);
            text-align: center;
            padding: 10px 20px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .logo img {
            width: 90px;
            height: auto;
            display: block;
            margin: 0 auto 15px;
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            color: #1cab6a;
            margin-top: 8px;
            margin-bottom: 0;
        }

        .content {
            padding: 35px 30px;
            font-size: 15px;
            color: #4b5563;
        }

        .content p {
            margin: 0 0 15px;
        }

        .button {
            display: inline-block;
            background: #1cab6a;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 15px;
        }

        .footer {
            padding: 18px 25px;
            background: #1cab6a;
            color: #ffffff;
            font-size: 13px;
            text-align: center;
        }

        .footer a {
            color: #ffffff;
            text-decoration: underline;
        }

        @media only screen and (max-width: 600px) {
            .email-wrapper {
                border-radius: 8px;
            }

            .content {
                padding: 25px 20px;
            }

            .header-title {
                font-size: 20px;
            }

            .logo img {
                max-width: 140px;
            }
        }
    </style>
</head>

<body>

    <div class="email-container">
        <div class="email-wrapper">

            <div class="header">
                <div class="logo">
                    <img src="{{ asset('frontend/assets/images/logo.webp') }}" alt="{{ config('app.name') }}">
                </div>

                <h1 class="header-title">
                    {{ $headerTitle ?? config('app.name') }}
                </h1>
            </div>

            <div class="content">
                @yield('content')
            </div>

            <div class="footer">
                {{ $footerText ?? 'Thank you for choosing us' }} <br>
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>

        </div>
    </div>

</body>

</html>