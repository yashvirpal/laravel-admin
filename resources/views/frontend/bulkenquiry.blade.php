@extends('layouts.frontend')


@section('meta')
    <x-frontend.meta :model="$page" />
@endsection

@section('content')
    <section class="contact-sec">
        <div class="container my-5">
            <div class="row g-4">
                {!! $page->description !!}
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">

                            <h3 class="mb-3 text-center">Bulk Enquiry</h3>
                            <p class="text-muted text-center mb-4">
                                Fill the form below and our team will contact you shortly.
                            </p>

                            <form id="bulkEnquiryForm" method="POST" action="{{ route('bulkenquiry.submit') }}">
                                @csrf

                                <div class="row">
                                    <!-- Name -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Name *</label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter your name"
                                            id="name">
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" name="email" class="form-control" placeholder="Enter your email"
                                            id="email">
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone *</label>
                                        <input type="tel" name="phone" class="form-control" placeholder="Enter phone number"
                                            id="phone">
                                    </div>

                                    <!-- Company -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Company</label>
                                        <input type="text" name="company" class="form-control" placeholder="Company name"
                                            id="company">
                                    </div>

                                    <!-- Product -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Product Interested *</label>
                                        <select name="products[]" id="productSelect" class="form-control" multiple></select>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Quantity *</label>
                                        <input type="number" name="quantity" class="form-control" min="1"
                                            placeholder="Enter quantity" id="quantity">
                                    </div>

                                    <!-- Message -->
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label">Message *</label>
                                        <textarea name="message" class="form-control" rows="5"
                                            placeholder="Enter your requirement" id="message"></textarea>
                                    </div>

                                    <!-- Submit -->
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary px-5 mybtn">
                                            Submit Bulk Enquiry
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Success Message -->
                            <div id="successMessage" class="alert alert-success mt-3 d-none"></div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <x-frontend.intl-tel-input />
    <link href="{{ asset('backend/css/select2.min.css') }}" rel="stylesheet" />

    <script src="{{ asset('backend/js/select2.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            loadPhoneInput("#phone");
            const rules = [
                { selector: "#name", rule: "name" },
                { selector: "#email", rule: "email" },
                { selector: "#phone", rule: "phone" },
                { selector: "#productSelect", rule: "products" }, // ✅ FIXED
                { selector: "#quantity", rule: "quantity" },
                { selector: "#message", rule: "message" }
            ];

            initFormValidator("#bulkEnquiryForm", rules);
        });
        $('#productSelect').on('change', function () {
            this.dispatchEvent(new Event('input'));
        });
        $('#productSelect').select2({
            placeholder: 'Search product...',
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('searchproduct') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(item => ({
                            id: item.id,      // 👈 store ID (better than title)
                            text: item.title
                        }))
                    };
                }
            }
        });
    </script>
@endpush