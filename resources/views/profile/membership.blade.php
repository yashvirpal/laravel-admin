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
                        <!-- Membership Page -->
                        <div id="membership-page" class="page-content" >
                            <h2 class="page-title">Membership</h2>
                            <div class="text-center py-5">
                                <i class="fas fa-award" style="font-size: 60px; color: #ffaa3d; margin-bottom: 20px;"></i>
                                <h4 class="mb-3">No Active Membership</h4>
                                <p class="text-muted mb-4">Join our exclusive membership program to get special benefits and
                                    discounts!</p>
                                <button class="btn btn-primary-custom">Explore Membership Plans</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--  account section end here -->

@endsection