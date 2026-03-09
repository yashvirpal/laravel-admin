import JustValidate from "just-validate";

// ---------------------
// Global validation rules
// ---------------------
export const ValidationRules = {
    name: [
        { rule: "required", errorMessage: "Name is required" },
        { rule: "minLength", value: 3, errorMessage: "Minimum 3 characters required" },
        { rule: "maxLength", value: 25, errorMessage: "Maximum 25 characters allowed" },
    ],
    email: [
        { rule: "required", errorMessage: "Email is required" },
        { rule: "email", errorMessage: "Enter a valid email" },
    ],
    password: [
        { rule: "required", errorMessage: "Password is required" },
        { rule: "strongPassword", errorMessage: "Password must contain at least 8 characters, letters and numbers" },
    ],
    password_confirmation: [
        { rule: "required", errorMessage: "Confirm your password" },
        {
            validator: (value, fields) => value === fields['#password'].elem.value,
            errorMessage: "Passwords do not match"
        }
    ],
    message: [
        { rule: 'required' },
        { rule: 'minLength', value: 5 }
    ],
    phone: [
        { rule: "required", errorMessage: "Phone is required" },
        {
            rule: "function",
            validator: (value, fields) => {
                const input = fields["#phone"]?.elem;
                if (!input) return false;

                const iti = input.intlTelInputInstance || window._phoneInputs?.["#phone"];
                if (!iti) return false;

                if (input.dataset.itiReady !== "true") return false;

                const number = iti.getNumber();
                if (!number) return false;

                return iti.isValidNumber() === true;
            },
            errorMessage: "Enter a valid phone number"
        },
    ],
    tnc: [
        {
            validator: () => {
                const checkbox = document.querySelector("#tnc");
                return checkbox && checkbox.checked;
            },
            errorMessage: "You must accept the Terms & Conditions"
        }
    ],

    // -------------------------
    // Billing (Always Required)
    // -------------------------
    billing_first_name: [
        { rule: "required", errorMessage: "Billing First name is required" },
        { rule: "minLength", value: 3, errorMessage: "Minimum 3 characters required" },
        { rule: "maxLength", value: 25, errorMessage: "Maximum 25 characters allowed" },
    ],
    billing_last_name: [
        { rule: "required", errorMessage: "Billing Last name is required" },
        { rule: "minLength", value: 3, errorMessage: "Minimum 3 characters required" },
        { rule: "maxLength", value: 25, errorMessage: "Maximum 25 characters allowed" },
    ],
    billing_address_line1: [
        { rule: "required", errorMessage: "Billing address is required" }
    ],
    billing_city: [
        { rule: "required", errorMessage: "Billing city is required" },
        { rule: "minLength", value: 3 },
        { rule: "maxLength", value: 25 },
    ],
    billing_state: [
        { rule: "required", errorMessage: "Billing state is required" },
    ],
    billing_phone: [
        { rule: "required", errorMessage: "Billing phone is required" },
        {
            rule: "function",
            validator: (value, fields) => {
                const input = fields["#billing_phone"]?.elem;
                if (!input) return false;

                const iti = input.intlTelInputInstance || window._phoneInputs?.["#billing_phone"];
                if (!iti) return false;

                if (input.dataset.itiReady !== "true") return false;

                const number = iti.getNumber();
                if (!number) return false;

                return iti.isValidNumber() === true;
            },
            errorMessage: "Enter a valid phone number"
        }
    ],
    billing_zip: [
        { rule: "required", errorMessage: "Billing zip is required" },
        { rule: "number", errorMessage: "Please enter number" },
        { rule: "minLength", value: 6, errorMessage: "Please enter valid zip" },
        { rule: "maxLength", value: 6, errorMessage: "Please enter valid zip" },
    ],

    // -------------------------
    // Shipping (conditional)
    // -------------------------
    shipping_first_name: [
        { rule: "required", errorMessage: "Shipping first name is required" },
        { rule: "minLength", value: 3, errorMessage: "Minimum 3 characters required" },
        { rule: "maxLength", value: 25, errorMessage: "Maximum 25 characters allowed" },
    ],
    shipping_last_name: [
        { rule: "required", errorMessage: "Shipping last name is required" },
        { rule: "minLength", value: 3, errorMessage: "Minimum 3 characters required" },
        { rule: "maxLength", value: 25, errorMessage: "Maximum 25 characters allowed" },
    ],
    shipping_address_line1: [
        { rule: "required", errorMessage: "Shipping address is required" },
    ],
    shipping_city: [
        { rule: "required", errorMessage: "Shipping city is required" },
        { rule: "minLength", value: 3, errorMessage: "Minimum 3 characters required" },
        { rule: "maxLength", value: 25, errorMessage: "Maximum 25 characters allowed" },
    ],
    shipping_state: [
        { rule: "required", errorMessage: "Shipping state is required" },
    ],
    shipping_phone: [
        { rule: "required", errorMessage: "Shipping phone is required" },
        {
            rule: "function",
            validator: (value, fields) => {
                const input = fields["#shipping_phone"]?.elem;
                if (!input) return false;

                const iti = input.intlTelInputInstance || window._phoneInputs?.["#shipping_phone"];
                if (!iti) return false;

                if (input.dataset.itiReady !== "true") return false;

                const number = iti.getNumber();
                if (!number) return false;

                return iti.isValidNumber() === true;
            },
            errorMessage: "Enter a valid phone number",
        },
    ],
    shipping_zip: [
        { rule: "required", errorMessage: "Shipping ZIP is required" },
        { rule: "number", errorMessage: "ZIP must be numeric" },
        { rule: "minLength", value: 6, errorMessage: "Enter valid ZIP" },
        { rule: "maxLength", value: 6, errorMessage: "Enter valid ZIP" },
    ],
    order_notes: [
        { rule: "minLength", errorMessage: "Minimum 5 characters required", value: 5 },
    ]
};


// ---------------------
// Input restriction patterns
// ---------------------
const RestrictionPatterns = {
    phone: /^[0-9+]*$/,
    name: /^[a-zA-Z\s]*$/,
    number: /^[0-9]*$/,
    integer: /^[0-9]*$/,
    decimal: /^[0-9.]*$/,
    alpha: /^[a-zA-Z]*$/,
    alphanumeric: /^[a-zA-Z0-9]*$/,
};

/**
 * Restrict input by type with optional min/max length
 */
export function restrictInputByType(selector, type, minLength = 0, maxLength = Infinity) {
    const regex = RestrictionPatterns[type];
    if (!regex) return;

    document.querySelectorAll(selector).forEach(input => {
        input.addEventListener("keypress", e => {
            const char = String.fromCharCode(e.which);
            if (!regex.test(char)) {
                e.preventDefault();
                return;
            }
            if (input.value.length >= maxLength) {
                e.preventDefault();
            }
        });

        input.addEventListener("paste", e => {
            const paste = (e.clipboardData || window.clipboardData).getData("text");
            if (!regex.test(paste)) {
                e.preventDefault();
                return;
            }
            if ((input.value + paste).length > maxLength) {
                e.preventDefault();
            }
        });
    });
}


// ---------------------
// Initialize validator for a form
// ---------------------
export function initFormValidator(formSelector, fields = [], onSuccessCallback = null) {
    const form = document.querySelector(formSelector);
    if (!form) return;

    const validator = new JustValidate(formSelector, {
        focusInvalidField: true,
        validateBeforeSubmitting: true,
        errorLabelStyle: { color: "#e3342f", fontSize: "14px" }
    });

    const shipCheckbox = document.querySelector("#differentShipping");

    // -------------------------
    // INITIAL RULE BINDING
    // -------------------------
    fields.forEach(f => {
        const rules = ValidationRules[f.rule];
        if (!rules) return;

        const isShipping = f.selector.startsWith("#shipping_");

        // Skip shipping rules initially if unchecked
        if (isShipping && shipCheckbox && !shipCheckbox.checked) return;

        validator.addField(f.selector, rules);
    });

    // -------------------------
    // TOGGLE SHIPPING RULES
    // -------------------------
    if (shipCheckbox) {
        shipCheckbox.addEventListener("change", function () {
            fields.forEach(f => {
                if (!f.selector.startsWith("#shipping_")) return;

                const rules = ValidationRules[f.rule];
                if (!rules) return;

                if (this.checked) {
                    validator.addField(f.selector, rules);
                } else {
                    validator.removeField(f.selector);
                }
            });
        });
    }

    // -------------------------
    // AJAX SUBMIT
    // -------------------------
    validator.onSuccess(function (event) {
        event.preventDefault();

        const $form = $(event.target);

        $.ajax({
            url: $form.attr("action"),
            method: $form.attr("method") || "POST",
            data: $form.serialize(),
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            },
            beforeSend: function () {
                showLoader();
            },
            success(response) {
                hideLoader();

                if (response.message) {
                    toastr.success(response.message);
                }

                if (response.redirect_url) {
                    setTimeout(() => window.location.href = response.redirect_url, 2000);
                }
            },
            error(xhr) {
                hideLoader();

                if (xhr.status === 419) {
                    toastr.error(xhr.responseJSON?.message || 'Session expired. Please refresh the page.');
                    return;
                }

                if (xhr.responseJSON?.errors) {
                    Object.keys(xhr.responseJSON.errors).forEach(field => {
                        toastr.error(xhr.responseJSON.errors[field][0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'An error occurred. Please try again.');
                }
            }
        });
    });

    return validator;
}


// Show loader
export function showLoader() {
    const loader = document.getElementById('ajax-loader');
    if (loader) loader.classList.remove('hidden');
}

// Hide loader
export function hideLoader() {
    const loader = document.getElementById('ajax-loader');
    if (loader) loader.classList.add('hidden');
}