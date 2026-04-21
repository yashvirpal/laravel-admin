@extends('emails.admin.layout')

@section('content')

<div class="row">
    <span class="label">Subscriber Email</span>
    <div class="value">{{ $email }}</div>
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