@extends('layouts.frontend')


@section('meta')
    {{-- <x-frontend-meta :model="$page" /> --}}
@endsection

@section('content')
    <section class="contact-sec">
        <div class="container my-5">
            <div class="row g-4">
                    {!! $page->description !!}
            </div>
        </div>
    </section>
@endsection

@push('scripts')

@endpush