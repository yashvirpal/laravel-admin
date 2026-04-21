@extends('emails.layouts.master')

@section('content')
    <div class="row">
        <span class="label">Name</span>
        <div class="value">{{ $enquiry->name }}</div>
    </div>

    <div class="row">
        <span class="label">Email</span>
        <div class="value">{{ $enquiry->email }}</div>
    </div>

    <div class="row">
        <span class="label">Phone</span>
        <div class="value">{{ $enquiry->phone }}</div>
    </div>

    @if(!empty($enquiry->company))
        <div class="row">
            <span class="label">Company</span>
            <div class="value">{{ $enquiry->company }}</div>
        </div>
    @endif

    @if(!empty($enquiry->products))
        <div class="row">
            <span class="label">Products</span>
            <div class="value">
                {{ is_array($enquiry->products) ? implode(', ', $enquiry->products) : $enquiry->products }}
            </div>
        </div>
    @endif

    <div class="row">
        <span class="label">Message</span>
        <div class="value">{{ $enquiry->message }}</div>
    </div>

    <div class="row">
        <span class="label">IP Address</span>
        <div class="value">{{ $enquiry->ip_address }}</div>
    </div>

    <div class="row">
        <span class="label">User Agent</span>
        <div class="value">{{ $enquiry->user_agent ?? 'N/A' }}</div>
    </div>

    <div class="row">
        <span class="label">Browser</span>
        <div class="value">{{ $enquiry->browser ?? 'N/A' }}</div>
    </div>

    <div class="row">
        <span class="label">Platform</span>
        <div class="value">{{ $enquiry->platform ?? 'N/A' }}</div>
    </div>

    <div class="row">
        <span class="label">Device</span>
        <div class="value">{{ $enquiry->device ?? 'N/A' }}</div>
    </div>
@endsection