@extends('emails.layouts.master')

@section('content')

    <p style="margin: 0 0 16px; line-height: 1.7;">
        <strong>Hello,</strong>
    </p>

    <p style="margin: 0 0 16px; line-height: 1.7; color: #4b5563;">
        Thank you for subscribing to our newsletter 🎉
    </p>

    <p style="margin: 0 0 20px; line-height: 1.7; color: #4b5563;">
        You have been successfully added to our mailing list.
        From now on, you’ll be among the first to receive updates about
        new arrivals, exclusive offers, special promotions, and important announcements.
    </p>

    <div style="
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-left: 4px solid #1cab6a;
                border-radius: 10px;
                padding: 16px;
                margin: 20px 0;
            ">
        <p style="
                    margin: 0 0 8px;
                    font-size: 14px;
                    font-weight: 600;
                    color: #1cab6a;
                ">
            Subscription Email
        </p>

        <p style="
                    margin: 0;
                    font-size: 15px;
                    font-weight: 600;
                    color: #111827;
                ">
            {{ $subscriber->email }}
        </p>
    </div>

    <p style="margin: 0 0 16px; line-height: 1.7;">
        We’re excited to stay connected with you.
    </p>

    <p style="margin-top: 24px;">
        Regards,<br>
        <strong>{{ config('app.name') }}</strong>
    </p>

@endsection