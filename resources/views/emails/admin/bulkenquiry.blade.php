@extends('emails.admin.layout')

@section('content')
    <div class="row">
        <span class="label">Name</span>
        <div class="value">{{ $data['name'] }}</div>
    </div>

    <div class="row">
        <span class="label">Email</span>
        <div class="value">{{ $data['email'] }}</div>
    </div>

    <div class="row">
        <span class="label">Phone</span>
        <div class="value">{{ $data['phone'] }}</div>
    </div>

    @if(!empty($data['company']))
        <div class="row">
            <span class="label">Company</span>
            <div class="value">{{ $data['company'] }}</div>
        </div>
    @endif

    @if(!empty($data['products']))
        <div class="row">
            <span class="label">Products</span>
            <div class="value">
                {{ is_array($data['products']) ? implode(', ', $data['products']) : $data['products'] }}
            </div>
        </div>
    @endif

    <div class="row">
        <span class="label">Message</span>
        <div class="value message-box">{{ $data['message'] }}</div>
    </div>

    <div class="row">
        <span class="label">IP Address</span>
        <div class="value">{{ $data['ip_address'] }}</div>
    </div>

    <div class="row">
        <span class="label">Browser</span>
        <div class="value">{{ $data['browser'] ?? 'N/A' }}</div>
    </div>

    <div class="row">
        <span class="label">Platform</span>
        <div class="value">{{ $data['platform'] ?? 'N/A' }}</div>
    </div>

    <div class="row">
        <span class="label">Device</span>
        <div class="value">{{ $data['device'] ?? 'N/A' }}</div>
    </div>
@endsection