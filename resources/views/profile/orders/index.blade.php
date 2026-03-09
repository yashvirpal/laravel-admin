@extends('layouts.frontend')

@section('meta')
    {{-- <x-frontend-meta :model="$page" /> --}}
@endsection

@section('content')
    <!--  account section start here -->
    <section class="account-sec">
        <div class="container mt-4 mb-5">
           
            <div class="row">
                @include('profile.partials.sidebar')

                <!-- Main Content -->
                <div class="col-lg-9 col-md-8">
                    <div class="main-content">
                        <!-- Orders Page -->
                        <div id="orders-page" class="page-content">
                            <h2 class="page-title">My orders</h2>
                            <div class="row mb-4">
                                <x-frontend.user.order-list :items="$orders" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--  account section end here -->

@endsection