@extends('emails.layouts.master')

@section('content')

    <p style="margin-bottom: 20px;">
        <strong>New Bulk Enquiry Received</strong>
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            border-collapse: collapse;
            font-size: 14px;
        ">
        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb; width: 140px;">Name</td>
            <td style="padding: 12px;">{{ $enquiry->name }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Email</td>
            <td style="padding: 12px;">{{ $enquiry->email }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Phone</td>
            <td style="padding: 12px;">{{ $enquiry->phone ?? 'N/A' }}</td>
        </tr>

        @if(!empty($enquiry->company))
            <tr>
                <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Company</td>
                <td style="padding: 12px;">{{ $enquiry->company }}</td>
            </tr>
        @endif

        @if(!empty($enquiry->products))
            <tr>
                <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Products</td>
                <td style="padding: 12px; line-height: 1.6;">
                    {{ is_array($enquiry->products) ? implode(', ', $enquiry->products) : $enquiry->products }}
                </td>
            </tr>
        @endif

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Message</td>
            <td style="padding: 12px; line-height: 1.7;">
                {{ $enquiry->message }}
            </td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">IP Address</td>
            <td style="padding: 12px;">{{ $enquiry->ip_address ?? 'N/A' }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Browser</td>
            <td style="padding: 12px;">{{ $enquiry->browser ?? 'N/A' }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Platform</td>
            <td style="padding: 12px;">{{ $enquiry->platform ?? 'N/A' }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">Device</td>
            <td style="padding: 12px;">{{ $enquiry->device ?? 'N/A' }}</td>
        </tr>

        <tr>
            <td style="padding: 12px; font-weight: 600; background: #f9fafb;">User Agent</td>
            <td style="padding: 12px; font-size: 12px; color: #6b7280;">
                {{ $enquiry->user_agent ?? 'N/A' }}
            </td>
        </tr>
    </table>

@endsection