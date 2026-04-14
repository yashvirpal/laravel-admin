<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Bulk Enquiry</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f7f7f7; padding:20px;">

    <div style="max-width:600px; margin:auto; background:#fff; padding:20px; border-radius:8px;">

        <h2 style="color:#333;">New Bulk Enquiry Received</h2>

        <p>You have received a new enquiry with the following details:</p>

        <table style="width:100%; border-collapse:collapse; margin-top:15px;">
            <tr>
                <td><strong>Name:</strong></td>
                <td>{{ $data['name'] }}</td>
            </tr>

            <tr>
                <td><strong>Email:</strong></td>
                <td>{{ $data['email'] }}</td>
            </tr>

            <tr>
                <td><strong>Phone:</strong></td>
                <td>{{ $data['phone'] }}</td>
            </tr>

            @if(!empty($data['company']))
            <tr>
                <td><strong>Company:</strong></td>
                <td>{{ $data['company'] }}</td>
            </tr>
            @endif

            <tr>
                <td><strong>Products:</strong></td>
                <td>
                    @if(!empty($data['products']))
                        {{ is_array($data['products']) ? implode(', ', $data['products']) : $data['products'] }}
                    @endif
                </td>
            </tr>

            <tr>
                <td><strong>Message:</strong></td>
                <td>{{ $data['message'] }}</td>
            </tr>

            <tr>
                <td><strong>IP Address:</strong></td>
                <td>{{ $data['ip_address'] }}</td>
            </tr>

            <tr>
                <td><strong>Browser:</strong></td>
                <td>{{ $data['browser'] ?? 'N/A' }}</td>
            </tr>

            <tr>
                <td><strong>Platform:</strong></td>
                <td>{{ $data['platform'] ?? 'N/A' }}</td>
            </tr>

            <tr>
                <td><strong>Device:</strong></td>
                <td>{{ $data['device'] ?? 'N/A' }}</td>
            </tr>

        </table>

        <hr style="margin:20px 0;">

        <p style="font-size:12px; color:#888;">
            This email was generated automatically from your website bulk enquiry form.
        </p>

    </div>

</body>
</html>