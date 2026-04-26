@extends('emails.layouts.master')

@section('content')

    <p style="margin-bottom: 20px;">
        <strong>New Contact Form Submission Received</strong>
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                overflow: hidden;
                font-size: 14px;
                border-collapse: collapse;
            ">
        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb; width: 140px;">Name</td>
            <td style="padding: 12px;">{{ $contact->name }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Email</td>
            <td style="padding: 12px;">{{ $contact->email }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Phone</td>
            <td style="padding: 12px;">{{ $contact->phone ?? 'N/A' }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Message</td>
            <td style="padding: 12px; line-height: 1.6;">
                {{ $contact->message }}
            </td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">IP Address</td>
            <td style="padding: 12px;">{{ $contact->ip_address ?? 'N/A' }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Browser</td>
            <td style="padding: 12px;">{{ $contact->browser ?? 'N/A' }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Platform</td>
            <td style="padding: 12px;">{{ $contact->platform ?? 'N/A' }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Device</td>
            <td style="padding: 12px;">{{ $contact->device ?? 'N/A' }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">User Agent</td>
            <td style="padding: 12px; font-size: 12px; color: #6b7280;">
                {{ $contact->user_agent ?? 'N/A' }}
            </td>
        </tr>
    </table>

@endsection