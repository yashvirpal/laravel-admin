@extends('emails.layouts.master')

@section('content')

    <p style="margin-bottom: 16px;">
        <strong>Dear {{ $enquiry->name }},</strong>
    </p>

    <p style="margin-bottom: 16px; line-height: 1.7;">
        Thank you for reaching out to us with your bulk enquiry.
        We have successfully received your request and appreciate your interest in our products.
    </p>

    <p style="margin-bottom: 20px; line-height: 1.7;">
        Our team is currently reviewing the details you shared and will get back to you shortly with the best possible
        assistance.
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
            Submitted Details
        </p>

        <p style="
                        margin: 0;
                        line-height: 1.7;
                        color: #4b5563;
                    ">
            {{ $enquiry->message }}
        </p>
    </div>

    <p style="margin-top: 20px; line-height: 1.7;">
        Thank you for your interest. We look forward to assisting you soon.
    </p>

    <p style="margin-top: 24px;">
        Regards,<br>
        <strong>{{ config('app.name') }}</strong>
    </p>

@endsection