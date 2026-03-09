@extends('layouts.frontend')


@section('meta')
    {{-- <x-frontend-meta :model="$page" /> --}}
@endsection

@section('content')
    <!-- Register section start here -->
    <section class="login-sec">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="login-container">
                        <div class="login-form">
                            <div class="login-form-inner">
                                <h1>Register</h1>
                                <form method="POST" action="{{ route('register') }}" id="registerForm">
                                    @csrf

                                    <div class="login-form-group">
                                        <label for="email">Name</label>
                                        <input type="text" name="name" id="name" placeholder="Name" autocomplete="off"
                                            autofocus>
                                        <small class="text-danger error_name error"></small>
                                    </div>

                                    <div class="login-form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" placeholder="Email" autocomplete="off">
                                        <small class="text-danger error_email error"></small>
                                    </div>

                                    <div class="login-form-group">
                                        <label for="phone">Phone</label>
                                        <input type="text" name="phone" id="phone" placeholder="Phone" class="w-100"
                                            autocomplete="off">
                                        <small class="text-danger error_phone error"></small>
                                    </div>

                                    <div class="login-form-group position-relative">
                                        <label for="password">Password</label>
                                        <input type="password" name="password" id="password" placeholder="Password"
                                            autocomplete="off">
                                        {{-- <span class="toggle-password" id="togglePass">
                                            <i class="fas fa-eye-slash" id="eyeIcon"></i>
                                        </span> --}}
                                        <small class="text-danger error_password error"></small>
                                    </div>

                                    <div class="login-form-group">
                                        <label for="password_confirmation">Confirm Password</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            placeholder="Confirm Password" autocomplete="off">
                                        {{-- <span class="toggle-password" id="togglePass">
                                            <i class="fas fa-eye-slash" id="eyeIcon"></i>
                                        </span> --}}
                                        <small class="text-danger error_password_confirmation error"></small>
                                    </div>
                                    <span class="my-1 msg h6"></span>
                                    <button type="submit" class="rounded-button login-cta register-link">
                                        Register
                                    </button>

                                    <div class="register-div">
                                        Already have an account?
                                        <a href="{{ route('login') }}" class="link create-account login-link">
                                            Log in here
                                        </a>
                                    </div>
                                </form>
                            </div>

                        </div>
                        <div class="login-img-wrap">
                            <figure> <img src="{{ asset('frontend/assets/images/re.png') }}" alt=""> </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Register section end here -->
@endsection
@push('scripts')
    <x-frontend.intl-tel-input />
    <script>

        document.addEventListener("DOMContentLoaded", () => {
            loadPhoneInput("#phone");
            const rules = [
                { selector: "#name", rule: "name" },
                { selector: "#email", rule: "email" },
                { selector: "#phone", rule: "phone" },
                { selector: "#password", rule: "password" },
                { selector: "#password_confirmation", rule: "password_confirmation" },
                //  { selector: "#tnc", rule: "tnc" }
            ];
            setTimeout(() => {
                initFormValidator("#registerForm", rules);
            }, 500)

        });
    </script>
@endpush