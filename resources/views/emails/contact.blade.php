<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Contact Message</title>
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
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .heading {
            font-size: 20px;
            margin-bottom: 20px;
            color: #111827;
        }

        .label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 4px;
        }

        .value {
            margin-bottom: 15px;
            color: #1f2937;
        }

        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="heading">📩 New Contact Message</div>
        <div>
            <div class="label">Name:</div>
            <div class="value">{{ $request->name }}</div>
            <div class="label">Email:</div>
            <div class="value">{{ $request->email }}</div>
            <div class="label">Phone:</div>
            <div class="value">{{ $request->phone }}</div>
            <div class="label">Message:</div>
            <div class="value">{{ $request->message }}</div>
        </div>
        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }} — Contact Form Notification
        </div>
    </div>
</body>

</html>