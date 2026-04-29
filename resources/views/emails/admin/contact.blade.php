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

    </table>

@endsection