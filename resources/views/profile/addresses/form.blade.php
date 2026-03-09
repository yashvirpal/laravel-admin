@extends('layouts.frontend')

@section('content')
    <section class="account-sec">
        <div class="container mt-4 mb-5">
            <!-- <h1 class="mb-4">My Account</h1> -->

            <div class="row">
                @include('profile.partials.sidebar')

                <div class="col-lg-9 col-md-8">
                    <div class="main-content">
                        <div class="page-content">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="page-title mb-0">
                                    {{ isset($address) ? 'Edit Address' : 'Add New Address' }}
                                </h2>

                                <a href="{{ route('profile.addresses') }}" class="btn btn-outline-secondary btn-sm">
                                    Back
                                </a>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" id="myForm"
                                        action="{{ isset($address) ? route('profile.addresses.update', $address) : route('profile.addresses.store') }}">
                                        @csrf
                                        @if(isset($address))
                                            @method('PUT')
                                        @endif
                                        <div class="row">

                                            <div class="col-md-12 mb-3">
                                                <label class="form-label d-block">Address Type</label>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="type" id="billing"
                                                        value="billing" {{ old('type', $address->type ?? '') == 'billing' ? 'checked' : '' }} />
                                                    <label class="form-check-label" for="billing">Billing</label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="type" id="shipping"
                                                        value="shipping" {{ old('type', $address->type ?? '') == 'shipping' ? 'checked' : '' }} />
                                                    <label class="form-check-label" for="shipping">Shipping</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">First Name</label>
                                                <input id="first_name" type="text" name="first_name" class="form-control"
                                                    value="{{ old('first_name', $address->first_name ?? '') }}" />
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Last Name</label>
                                                <input id="last_name" type="text" name="last_name" class="form-control"
                                                    value="{{ old('last_name', $address->last_name ?? '') }}" />
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Company (Optional)</label>
                                                <input id="company" type="text" name="company" class="form-control"
                                                    value="{{ old('company', $address->company ?? '') }}">
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label">Address Line 1</label>
                                                <input type="text" id="address_line1" name="address_line1"
                                                    class="form-control"
                                                    value="{{ old('address_line1', $address->address_line1 ?? '') }}" />
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label">Address Line 2</label>
                                                <input type="text" id="address_line2" name="address_line2"
                                                    class="form-control"
                                                    value="{{ old('address_line2', $address->address_line2 ?? '') }}">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="text" id="phone" name="phone" class="form-control"
                                                    value="{{ old('phone', $address->phone ?? '') }}" />
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">City</label>
                                                <input type="text" id="city" name="city" class="form-control"
                                                    value="{{ old('city', $address->city ?? '') }}" />
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">State</label>
                                                <input type="text" id="state" name="state" class="form-control"
                                                    value="{{ old('state', $address->state ?? '') }}" />
                                            </div>


                                            <div class="col-md-6 mb-3 d-none">
                                                <label class="form-label">Country</label>
                                                <input type="text" id="country" name="country" class="form-control"
                                                    value="{{ old('country', $address->country ?? 'India') }}" />
                                            </div>


                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Zip Code</label>
                                                <input type="text" id="zip" name="zip" class="form-control"
                                                    value="{{ old('zip', $address->zip ?? '') }}" />
                                            </div>


                                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                                <div class="form-check">
                                                    <input class="form-check-input" id="is_default" type="checkbox"
                                                        name="is_default" value="1" {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }} />
                                                    <label class="form-check-label" for="is_default">
                                                        Set as default address
                                                    </label>
                                                </div>
                                            </div>

                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            {{ isset($address) ? 'Update Address' : 'Save Address' }}
                                        </button>

                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <x-frontend.intl-tel-input />
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            loadPhoneInput("#phone");
            const rules = [
                { selector: "#first_name", rule: "first_name" },
                { selector: "#last_name", rule: "last_name" },
                { selector: "#company", rule: "company" },
                { selector: "#address_line1", rule: "address_line1" },
                { selector: "#address_line2", rule: "address_line2" },
                { selector: "#phone", rule: "phone" },
                { selector: "#city", rule: "city" },
                { selector: "#state", rule: "state" },
                { selector: "#country", rule: "country" },
                { selector: "#zip", rule: "zip" }
            ];

            initFormValidator("#myForm", rules);
        });
    </script>
@endpush