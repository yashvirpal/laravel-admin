@extends('layouts.admin')

@section('content')
    @php
        $title = 'Settings';
        $breadcrumbs = [
            'Home' => route('admin.dashboard'),
            'Settings' => ''
        ];
    @endphp
    <div class="card card-primary card-outline mb-4">
        <div class="card-header">
            <h5>Settings</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Tabs -->
                <ul class="nav nav-tabs" id="settingsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                            type="button" role="tab">General</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment"
                            type="button" role="tab">Payment</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping"
                            type="button" role="tab">Shipping</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="smtp-tab" data-bs-toggle="tab" data-bs-target="#smtp" type="button"
                            role="tab">SMTP</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="script-tab" data-bs-toggle="tab" data-bs-target="#script" type="button"
                            role="tab">Script</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button"
                            role="tab">Social</button>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="settingsTabContent">

                    <!-- General Tab -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Favicon</label>
                                <input type="file" name="favicon" class="form-control">
                                @if(!empty($settings['favicon']))
                                    <img src="{{  $settings['favicon_url'] ?? '' }} " class="mt-2" alt="Header Logo"
                                        height="50">
                                @endif
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Header Logo</label>
                                <input type="file" name="header_logo" class="form-control">
                                @if(!empty($settings['header_logo']))
                                    <img src="{{  $settings['header_logo_url'] ?? '' }} " class="mt-2" alt="Header Logo"
                                        height="50">
                                @endif

                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Footer Logo</label>
                                <input type="file" name="footer_logo" class="form-control">
                                @if(!empty($settings['footer_logo']))
                                    <img src="{{  $settings['footer_logo_url'] ?? '' }} " class="mt-2" alt="Footer Logo"
                                        height="50">
                                @endif
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Currency</label>
                                <select name="currency" class="form-control">
                                    @foreach($currencies as $code => $symbol)
                                        <option value="{{ $code . ',' . $symbol }}" {{ old('currency', $settings['currency'] ?? '') == $code . ',' . $symbol ? 'selected' : '' }}>
                                            {{ $code }} ({{ $symbol }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $settings['email'] ?? '') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Admin Email</label>
                                <input type="email" name="email2" class="form-control"
                                    value="{{ old('admin_email', $settings['admin_email'] ?? '') }}">
                            </div>
                            <!-- <div class="col-md-3 mb-3">
                                    <label class="form-label fw-semibold">Email 2</label>
                                    <input type="email" name="email2" class="form-control"
                                        value="{{ old('email2', $settings['email2'] ?? '') }}">
                                </div> -->

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $settings['phone'] ?? '') }}">
                            </div>
                            <!-- <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Phone 2</label>
                                <input type="text" name="phone2" class="form-control"
                                    value="{{ old('phone2', $settings['phone2'] ?? '') }}">
                            </div> -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Address</label>
                                <textarea name="address"
                                    class="form-control">{{ old('address', $settings['address'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Map Embed</label>
                                <textarea name="map"
                                    class="form-control">{{ old('map', $settings['map'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Tab -->
                    <div class="tab-pane fade" id="payment" role="tabpanel">
                        <div class="row">
                            @foreach($paymentGateways as $pg)
                                @php
                                    $pgData = $settings['payment_gateways'][$pg] ?? [];
                                @endphp



                                <div class="col-md-12 mb-4 border rounded p-3 bg-light">
                                    <h6 class="fw-semibold text-capitalize">{{ $pg }} Settings</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox"
                                            name="payment_gateways[{{ $pg }}][enabled]" value="1" {{ !empty($pgData['enabled']) ? 'checked' : '' }}>
                                        <label class="form-check-label">Enabled</label>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <input type="text" name="payment_gateways[{{ $pg }}][description]" class="form-control"
                                            placeholder="Description" value="{{ $pgData['description'] ?? '' }}">
                                    </div>
                                    @if ($pg !== 'cod')
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <input type="text" name="payment_gateways[{{ $pg }}][merchant_id]"
                                                    class="form-control" placeholder="Merchant ID"
                                                    value="{{ $pgData['merchant_id'] ?? '' }}">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <input type="text" name="payment_gateways[{{ $pg }}][secret_key]"
                                                    class="form-control" placeholder="Secret Key"
                                                    value="{{ $pgData['secret_key'] ?? '' }}">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <input type="text" name="payment_gateways[{{ $pg }}][webhook_key]"
                                                    class="form-control" placeholder="Webhook Key"
                                                    value="{{ $pgData['webhook_key'] ?? '' }}">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <input type="url" name="payment_gateways[{{ $pg }}][webhook_url]"
                                                    class="form-control" placeholder="Webhook URL"
                                                    value="{{ $pgData['webhook_url'] ?? '' }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            @endforeach
                        </div>
                    </div>

                    <!-- Shippings Tab -->
                    <div class="tab-pane fade" id="shipping" role="tabpanel">
                        <div class="row">
                            @foreach($shippingMethods as $sm)
                                @php
                                    $smData = $settings['shipping_methods'][$sm] ?? [];
                                @endphp
                                <div class="col-md-12 mb-4 border rounded p-3 bg-light">
                                    <h6 class="fw-semibold text-capitalize">{{ labelFromKey($sm) }} Settings</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox"
                                            name="shipping_methods[{{ $sm }}][enabled]" value="1" {{ !empty($smData['enabled']) ? 'checked' : '' }}>
                                        <label class="form-check-label">Enabled</label>
                                    </div>
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-3">
                                            <input type="text" name="shipping_methods[{{ $sm }}][title]" class="form-control"
                                                placeholder="Title" value="{{ $smData['title'] ?? '' }}">
                                        </div> --}}

                                        <div class="col-md-6 mb-3">
                                            <input type="number" name="shipping_methods[{{ $sm }}][amount]" class="form-control"
                                                placeholder="Amount" value="{{ $smData['amount'] ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <input type="text" name="shipping_methods[{{ $pg }}][description]"
                                                class="form-control" placeholder="Description"
                                                value="{{ $smData['description'] ?? '' }}">
                                        </div>
                                    </div>

                                </div>

                            @endforeach
                        </div>
                    </div>

                    <!-- SMTP Tab -->
                    <div class="tab-pane fade" id="smtp" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">SMTP Host</label>
                                <input type="text" name="smtp_host" class="form-control"
                                    value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">SMTP Port</label>
                                <input type="number" name="smtp_port" class="form-control"
                                    value="{{ old('smtp_port', $settings['smtp_port'] ?? '') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">SMTP Username</label>
                                <input type="text" name="smtp_username" class="form-control"
                                    value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">SMTP Password</label>
                                <input type="text" name="smtp_password" class="form-control"
                                    value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">SMTP From</label>
                                <input type="text" name="smtp_from" class="form-control"
                                    value="{{ old('smtp_from', $settings['smtp_from'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Script Tab -->
                    <div class="tab-pane fade" id="script" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Head Script</label>
                            <textarea name="head_script"
                                class="form-control">{{ old('head_script', $settings['head_script'] ?? '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Body Script</label>
                            <textarea name="body_script"
                                class="form-control">{{ old('body_script', $settings['body_script'] ?? '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Footer Script</label>
                            <textarea name="footer_script"
                                class="form-control">{{ old('footer_script', $settings['footer_script'] ?? '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Footer Content</label>
                            <textarea name="footer_content"
                                class="form-control">{{ old('footer_content', $settings['footer_content'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Social Tab -->
                    <div class="tab-pane fade" id="social" role="tabpanel">
                        <div class="row">
                            @foreach($socialPlatforms as $platform)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">{{ ucfirst($platform) }} URL</label>
                                    <input type="url" name="social[{{ $platform }}]" class="form-control"
                                        value="{{ old('social.' . $platform, $settings['social'][$platform] ?? '') }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
            </form>
        </div>
    </div>
@endsection