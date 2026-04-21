@extends('emails.layouts.master')

@section('content')

    <p>Dear {{ $request->name }},</p>

    <p>
        Thank you for contacting us. We have successfully received your message.
    </p>

    <p>
        Our team will review your enquiry and get back to you shortly.
    </p>

    <div class="row">
        <span class="label">Your Message</span>
        <div class="value">{{ $request->message }}</div>
    </div>

    <p>
        Thanks & Regards,<br>
        {{ config('app.name') }}
    </p>

@endsection