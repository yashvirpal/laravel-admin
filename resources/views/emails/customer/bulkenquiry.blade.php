@extends('emails.layouts.master')

@section('content')
    <p>Dear {{ $enquiry->name }},</p>

    <p>
        Thank you for reaching out to us with your bulk enquiry.
        We have successfully received your request.
    </p>

    <p>
        Our team will review your enquiry and get back to you shortly.
    </p>

    <div class="row">
        <span class="label">Submitted Details</span>
        <div class="value">
            {{ $enquiry->message }}
        </div>
    </div>

    <p>Thank you for your interest.</p>
@endsection