@extends('emails.layouts.master')

@section('content')

    <p>Hello,</p>

    <p>
        Thank you for subscribing to our newsletter.
    </p>

    <p>
        You will now receive our latest updates, offers, and news directly in your inbox.
    </p>

    <div class="row">
        <span class="label">Subscribed Email</span>
        <div class="value">{{ $subscriber->email }}</div>
    </div>

    <p>
        Thank you for staying connected with us.
    </p>

@endsection