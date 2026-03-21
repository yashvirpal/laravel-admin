<!-- Billing Details -->
<div class="billing-section">
    <h5 class="section-header">
        <i class="fas fa-file-invoice"></i> Billing Details
    </h5>
    <div class="row">

        <input type="hidden" name="billing_address_id" value="{{ old('billing_address_id', $billingAddress->id ?? '')}}" />
        <input type="hidden" name="shipping_address_id" value="{{ old('shipping_address_id', $shippingAddress->id ?? '')}}" />
        <div class="col-md-6">
            <div class="input-field">
                <label>First Name <span class="required-star">*</span></label>
                <input name="billing_first_name" id="billing_first_name" type="text" placeholder="First Name"
                    value="{{ old('billing_first_name', $billingAddress->first_name ?? '')}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-field">
                <label>Last Name <span class="required-star">*</span></label>
                <input name="billing_last_name" id="billing_last_name" type="text" placeholder="Last Name"
                    value="{{ old('billing_last_name', $billingAddress->last_name ?? '')}}">
            </div>
        </div>
    </div>

    <div class="input-field">
        <label>Street Address <span class="required-star">*</span></label>
        <input name="billing_address_line1" id="billing_address_line1" type="text"
            placeholder="House number and street name" class="mb-3"
            value="{{ old('billing_address_line1', $billingAddress->address_line1 ?? '')}}">
        <input name="billing_address_line2" id="billing_address_line2" type="text"
            value="{{ old('billing_address_line2', $billingAddress->address_line2 ?? '')}}"
            placeholder="Apartment, suite, unit, etc. (optional)">
    </div>

    <div class="input-field">
        <label>City <span class="required-star">*</span></label>
        <input name="billing_city" id="billing_city" type="text" placeholder="Your city"
            value="{{ old('billing_city', $billingAddress->city ?? '')}}">
    </div>

    <div class="input-field">
        <label>State <span class="required-star">*</span></label>
        <input name="billing_state" id="billing_state" type="text" placeholder="Your State"
            value="{{ old('billing_state', $billingAddress->state ?? '')}}">
    </div>

    <div class="input-field">
        <label>Phone <span class="required-star">*</span></label>
        <input name="billing_phone" id="billing_phone" class="w-100" type="tel"
            value="{{ old('billing_phone', $billingAddress->phone ?? '')}}" placeholder="+1 234 567 8900"
            data-phone-input>
        <input type="hidden" id="billing_phone_full" name="billing_phone_full">
        <div class="phone-error"></div>
    </div>

    <div class="input-field">
        <label>Zip <span class="required-star">*</span></label>
        <input name="billing_zip" id="billing_zip" type="tel"
            value="{{ old('billing_zip', $billingAddress->zip ?? '')}}" placeholder="110096">
    </div>

    @if (Auth::guest())
        <div class="input-field">
            <label>Email Address <span class="required-star">*</span></label>
            <input name="email" id="email" type="email" value="{{ old('email', auth()->user()->email ?? '')}}"
                placeholder="your.email@example.com">
        </div>
        <div class="input-field">
            <label>Password <span class="required-star">*</span></label>
            <input name="password" id="password" type="password" placeholder="Password">
        </div>
    @endif


    <!-- Different Shipping -->
    <div class="notification-card" style="margin-top: 25px;">
        <div class="checkbox-group d-none" style="margin: 0;">

            <input type="checkbox" name="differentShipping" id="differentShipping"
                onclick="toggleContent('shippingContent')">
            <label for="differentShipping">Ship to a different address?</label>
        </div>
        <div id="shippingContent" class="expandable-content active">
            <h5 class="section-header"> <i class="fas fa-shipping-fast"></i> Shipping Address </h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="input-field">
                        <label>First Name <span class="required-star">*</span></label>
                        <input name="shipping_first_name" id="shipping_first_name" type="text"
                            value="{{ old('shipping_first_name', $shippingAddress->first_name ?? '')}}"
                            placeholder="First Name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-field">
                        <label>Last Name <span class="required-star">*</span></label>
                        <input name="shipping_last_name" id="shipping_last_name" type="text"
                            value="{{ old('shipping_last_name', $shippingAddress->last_name ?? '')}}"
                            placeholder="Last Name">
                    </div>
                </div>
            </div>

            <div class="input-field">
                <label>Street Address <span class="required-star">*</span></label>
                <input name="shipping_address_line1" id="shipping_address_line1" type="text"
                    placeholder="House number and street name"
                    value="{{ old('shipping_address_line1', $shippingAddress->address_line1 ?? '')}}" class="mb-3">
                <input name="shipping_address_line2" id="shipping_address_line2" type="text"
                    value="{{ old('shipping_address_line2', $shippingAddress->address_line2 ?? '')}}"
                    placeholder="Apartment, suite, unit, etc. (optional)">
            </div>

            <div class="input-field">
                <label> City <span class="required-star">*</span></label>
                <input name="shipping_city" id="shipping_city" type="text"
                    value="{{ old('shipping_city', $shippingAddress->city ?? '')}}" placeholder="Your city">
            </div>

            <div class="input-field">
                <label>State <span class="required-star">*</span></label>
                <input name="shipping_state" id="shipping_state" type="text"
                    value="{{ old('shipping_state', $shippingAddress->state ?? '')}}" placeholder="Your State">
            </div>

            <div class="input-field">
                <label>Phone <span class="required-star">*</span></label>
                <input name="shipping_phone" id="shipping_phone" class="w-100" type="tel"
                    value="{{ old('shipping_phone', $shippingAddress->phone ?? '')}}" placeholder="+1 234 567 8900"
                    data-phone-input>
                <input type="hidden" id="shipping_phone_full" name="shipping_phone_full">
                <div class="phone-error"></div>
            </div>
            <div class="input-field">
                <label>Zip <span class="required-star">*</span></label>
                <input name="shipping_zip" id="shipping_zip" type="tel"
                    value="{{ old('shipping_zip', $shippingAddress->zip ?? '')}}" placeholder="110096">
            </div>
        </div>
    </div>

    <div class="input-field" style="margin-top: 25px;">
        <label>Other Notes (optional)</label>
        <textarea name="order_notes" id="order_notes" rows="4" placeholder="special notes for delivery."></textarea>
    </div>

</div>