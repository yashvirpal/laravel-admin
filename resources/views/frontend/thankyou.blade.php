@extends('layouts.frontend')

@section('meta')
    <x-frontend.meta :model="$page" />
@endsection

@section('content')
    <section class="contact-sec">
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    {{ $page->description ?? "" }}

                </div>
            </div>

        </div>
    </section>
@endsection