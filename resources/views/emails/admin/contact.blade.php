@extends('emails.layouts.master')

@section('content')

    <div class="row">
        <span class="label">Name</span>
        <div class="value">{{ $contact->name }}</div>
    </div>

    <div class="row">
        <span class="label">Email</span>
        <div class="value">{{ $contact->email }}</div>
    </div>

    <div class="row">
        <span class="label">Phone</span>
        <div class="value">{{ $contact->phone }}</div>
    </div>

    <div class="row">
        <span class="label">Message</span>
        <div class="value">{{ $contact->message }}</div>
    </div>

    <div class="row">
        <span class="label">IP Address</span>
        <div class="value">{{ $contact->ip_address }}</div>
    </div>

    <div class="row">
        <span class="label">User Agent</span>
        <div class="value">{{ $contact->user_agent ?? 'N/A' }}</div>
    </div>

    <div class="row">
        <span class="label">Browser</span>
        <div class="value">{{ $contact->browser ?? 'N/A' }}</div>
    </div>

    <div class="row">
        <span class="label">Platform</span>
        <div class="value">{{ $contact->platform ?? 'N/A' }}</div>
    </div>

    <div class="row">
        <span class="label">Device</span>
        <div class="value">{{ $contact->device ?? 'N/A' }}</div>
    </div>

@endsection