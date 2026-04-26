@extends('emails.layouts.master')

@section('content')

    <p style="margin-bottom: 16px;">
        <strong>Dear {{ $contact->name }},</strong>
    </p>

    <p style="margin-bottom: 16px;">
        Thank you for contacting us. We have successfully received your message.
    </p>

    <p style="margin-bottom: 20px;">
        Our team will review your enquiry and get back to you shortly.
    </p>

    <div style="
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-left: 4px solid #1cab6a;
                padding: 16px;
                border-radius: 8px;
                margin: 20px 0;
            ">
        <p style="
                    margin: 0 0 8px;
                    font-size: 14px;
                    font-weight: 600;
                    color: #1cab6a;
                ">
            Your Message
        </p>

        <p style="
                    margin: 0;
                    color: #4b5563;
                    line-height: 1.7;
                ">
            {{ $contact->message }}
        </p>
    </div>

    <p style="margin-top: 24px;">
        Thanks & Regards,<br>
        <strong>{{ config('app.name') }}</strong>
    </p>

@endsection