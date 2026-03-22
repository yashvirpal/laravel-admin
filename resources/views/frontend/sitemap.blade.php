@extends('layouts.frontend')

@section('meta')
   <x-frontend.meta :model="$page" />
@endsection

@section('content')
    <div class="container my-5">
            <div class="row g-4">
                {!! $page->description !!}
            </div>
        </div>
    </section>
@endsection