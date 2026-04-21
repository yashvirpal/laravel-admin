@extends('emails.admin.layout')

@section('content')

<div class="row">
    <span class="label">Name</span>
    <div class="value">{{ $request->name }}</div>
</div>

<div class="row">
    <span class="label">Email</span>
    <div class="value">{{ $request->email }}</div>
</div>

<div class="row">
    <span class="label">Phone</span>
    <div class="value">{{ $request->phone }}</div>
</div>

<div class="row">
    <span class="label">Message</span>
    <div class="value">{{ $request->message }}</div>
</div>

<div class="row">
    <span class="label">IP Address</span>
    <div class="value">{{ request()->ip() }}</div>
</div>

<div class="row">
    <span class="label">Browser</span>
    <div class="value">{{ $browser ?? 'N/A' }}</div>
</div>

<div class="row">
    <span class="label">Platform</span>
    <div class="value">{{ $platform ?? 'N/A' }}</div>
</div>

<div class="row">
    <span class="label">Device</span>
    <div class="value">{{ $device ?? 'N/A' }}</div>
</div>

@endsection