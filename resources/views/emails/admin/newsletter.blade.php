@extends('emails.layouts.master')

@section('content')

    <div class="row">
        <span class="label">Subscriber Email</span>
        <div class="value">{{ $subscriber->email }}</div>
    </div>

    <div class="row">
        <span class="label">IP Address</span>
        <div class="value">{{ $subscriber->ip_address }}</div>
    </div>

    <div class="row">
        <span class="label">User Agent</span>
        <div class="value">{{ $subscriber->user_agent ?? 'N/A' }}</div>
    </div>

    <div class="row">
        <span class="label">Browser</span>
        <div class="value">{{ $subscriber->browser ?? 'N/A' }}</div>
    </div>

    <div class="row">
        <span class="label">Platform</span>
        <div class="value">{{ $subscriber->platform ?? 'N/A' }}</div>
    </div>

    <div class="row">
        <span class="label">Device</span>
        <div class="value">{{ $subscriber->device ?? 'N/A' }}</div>
    </div>

@endsection