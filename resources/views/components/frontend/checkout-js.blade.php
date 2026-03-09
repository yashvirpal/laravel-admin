<script>
    document.addEventListener("DOMContentLoaded", () => {
        console.log('=== Checkout Page Initialized ===');
        
        // Initialize billing phone immediately
        console.log('Initializing billing phone...');
        loadPhoneInput("#billing_phone");
        
        // Check if shipping section is visible or needs initialization
        const shippingContent = document.getElementById('shippingContent');
        const differentShippingCheckbox = document.querySelector('#differentShipping');
        
        // Initialize shipping phone based on visibility
        if (shippingContent && (shippingContent.classList.contains('active') || differentShippingCheckbox?.checked)) {
            console.log('Shipping section visible, initializing shipping phone...');
            loadPhoneInput("#shipping_phone");
        }

        // Checkout form rules
        const checkoutRules = [
            // Billing
            { selector: "#billing_first_name", rule: "billing_first_name" },
            { selector: "#billing_last_name", rule: "billing_last_name" },
            { selector: "#billing_address_line1", rule: "billing_address_line1" },
            { selector: "#billing_city", rule: "billing_city" },
            { selector: "#billing_state", rule: "billing_state" },
            { selector: "#billing_phone", rule: "billing_phone" },
            { selector: "#billing_zip", rule: "billing_zip" },

            // Shipping (conditional)
            { selector: "#shipping_first_name", rule: "shipping_first_name" },
            { selector: "#shipping_last_name", rule: "shipping_last_name" },
            { selector: "#shipping_address_line1", rule: "shipping_address_line1" },
            { selector: "#shipping_city", rule: "shipping_city" },
            { selector: "#shipping_state", rule: "shipping_state" },
            { selector: "#shipping_phone", rule: "shipping_phone" },
            { selector: "#shipping_zip", rule: "shipping_zip" },

            { selector: "#order_notes", rule: "order_notes" },
        ];

        // Only add auth fields if they exist in the DOM (guest users)
        if (document.querySelector('#email')) {
            checkoutRules.push({ selector: "#email", rule: "email" });
            checkoutRules.push({ selector: "#password", rule: "password" });
        }

        setTimeout(() => {
            initFormValidator("#checkoutForm", checkoutRules);
        }, 500);

        // Login form rules (only if form exists)
        const loginForm = document.querySelector("#checkoutLoginForm");
        if (loginForm) {
            const loginRules = [
                { selector: "#login_email", rule: "email" },
                { selector: "#login_password", rule: "password" },
            ];
            setTimeout(() => {
                initFormValidator("#checkoutLoginForm", loginRules);
            }, 500);
        }

        // Toggle shipping fields
        if (differentShippingCheckbox) {
            differentShippingCheckbox.addEventListener('change', function(e) {
                console.log('Shipping checkbox changed:', e.target.checked);
                
                // Initialize shipping phone if not already initialized
                if (e.target.checked) {
                    const shippingPhoneInput = document.querySelector('#shipping_phone');
                    if (shippingPhoneInput && !shippingPhoneInput.dataset.itiInitialized) {
                        console.log('Initializing shipping phone on checkbox change...');
                        setTimeout(() => {
                            loadPhoneInput("#shipping_phone");
                        }, 300); // Wait for content to expand
                    }
                }
                
                const shippingFields = document.querySelectorAll('[id^="shipping_"]');
                shippingFields.forEach(el => {
                    // Don't disable hidden phone fields
                    if (el.id !== 'shipping_phone_full') {
                        el.disabled = !e.target.checked;
                        if (!e.target.checked && el.id !== 'shipping_phone') {
                            el.value = '';
                        }
                    }
                });
                
                // Handle phone input separately
                const shippingPhone = document.querySelector('#shipping_phone');
                if (shippingPhone && !e.target.checked) {
                    shippingPhone.value = '';
                    const shippingPhoneFull = document.querySelector('#shipping_phone_full');
                    if (shippingPhoneFull) shippingPhoneFull.value = '';
                }
            });
        }

        // Form submission handler
        const checkoutForm = document.querySelector('#checkoutForm');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                console.log('=== Form Submission Started ===');
                
                // Get phone numbers before submit
                const billingPhone = getPhoneNumber('#billing_phone');
                console.log('Billing Phone Data:', billingPhone);
                
                // Validate billing phone
                if (!billingPhone || !billingPhone.isValid) {
                    e.preventDefault();
                   // alert('Please enter a valid billing phone number');
                    toastr.error('Please enter a valid billing phone number');
                    return false;
                }
                
                // Update billing phone hidden field
                document.querySelector('#billing_phone_full').value = billingPhone.e164;
                
                // Handle shipping phone if different shipping is checked
                const differentShipping = document.querySelector('#differentShipping')?.checked;
                if (differentShipping) {
                    const shippingPhone = getPhoneNumber('#shipping_phone');
                    console.log('Shipping Phone Data:', shippingPhone);
                    
                    // Validate shipping phone
                    if (!shippingPhone || !shippingPhone.isValid) {
                        e.preventDefault();
                        //alert('Please enter a valid shipping phone number');
                        toastr.error('Please enter a valid shipping phone number');
                        return false;
                    }
                    
                    // Update shipping phone hidden field
                    document.querySelector('#shipping_phone_full').value = shippingPhone.e164;
                }
                
                console.log('=== All Phone Numbers Valid ===');
                console.log('Billing:', billingPhone.e164);
                if (differentShipping) {
                    const shippingPhone = getPhoneNumber('#shipping_phone');
                    console.log('Shipping:', shippingPhone.e164);
                }
            });
        }
    });
</script>