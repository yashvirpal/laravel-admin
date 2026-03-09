{{-- resources/views/components/frontend/intl-tel-input.blade.php --}}

@once
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/css/intlTelInput.min.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/intlTelInput.min.js"></script>

<style>
    .iti { width: 100%; }
    .phone-input.is-invalid { border-color: #dc3545 !important; }
    .phone-input.is-valid { border-color: #28a745 !important; }
    .phone-error { display: none; color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
    .phone-error.show { display: block; }
</style>

<script>
(function() {
    'use strict';
    
    window._phoneInputs = window._phoneInputs || {};
    window._detectedCountry = null; // Store detected country globally

    window.loadPhoneInput = function(selector = "#phone", customOptions = {}) {
        const input = document.querySelector(selector);
        
        if (!input) {
            console.warn(`⚠️ Phone input not found: ${selector}`);
            return null;
        }

        if (input.dataset.itiInitialized === "true") {
            return window._phoneInputs[selector];
        }

        const defaultOptions = {
            initialCountry: window._detectedCountry || "in", // Use detected country or India
            nationalMode: false,
            formatOnDisplay: true,
            separateDialCode: true,
            autoPlaceholder: "aggressive",
            preferredCountries: ['in', 'us', 'gb', 'ae', 'ca', 'au'],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/utils.js",
            
            // Use Laravel backend API for country detection
            geoIpLookup: function(callback) {
                // If we already detected the country, use it immediately
                if (window._detectedCountry) {
                    console.log('🌍 Using cached country:', window._detectedCountry);
                    callback(window._detectedCountry);
                    return;
                }

                console.log('🌍 Detecting country via Laravel API...');
                
                fetch("{{ route('api.geo.country') }}")
                    .then(response => {
                        if (!response.ok) throw new Error('API request failed');
                        return response.json();
                    })
                    .then(data => {
                        window._detectedCountry = data.country_code; // Cache the country
                        console.log('✓ Country detected:', data.country_code, '-', data.country_name);
                        console.log('📡 Source:', data.source);
                        callback(data.country_code);
                    })
                    .catch(error => {
                        console.warn('⚠️ Laravel API failed, using fallback...', error);
                        
                        // Client-side fallback if Laravel API fails
                        fetch("https://ipapi.co/json/")
                            .then(response => response.json())
                            .then(data => {
                                window._detectedCountry = data.country_code || 'IN';
                                console.log('✓ Country detected (client fallback):', window._detectedCountry);
                                callback(window._detectedCountry);
                            })
                            .catch(() => {
                                window._detectedCountry = 'IN';
                                console.warn('⚠️ All detection methods failed, using India');
                                callback("IN");
                            });
                    });
            }
        };

        const options = { ...defaultOptions, ...customOptions };
        const iti = window.intlTelInput(input, options);

        window._phoneInputs[selector] = iti;
        input.dataset.itiInitialized = "true";
        input.dataset.itiReady = "false";

        iti.promise.then(() => {
            input.dataset.itiReady = "true";
            
            // Force set country after initialization if not set
            setTimeout(() => {
                const selectedCountry = iti.getSelectedCountryData();
                
                if (!selectedCountry.iso2 && window._detectedCountry) {
                    console.log('🔧 Forcing country to:', window._detectedCountry);
                    iti.setCountry(window._detectedCountry);
                }
                
                // Log final state
                const finalCountry = iti.getSelectedCountryData();
                const countryName = finalCountry.name || 
                                  (finalCountry.iso2 ? finalCountry.iso2.toUpperCase() : '') || 
                                  'India';
                console.log("☎️ IntlTelInput Ready for", selector, "- Country:", countryName, 
                           "| Dial Code: +" + (finalCountry.dialCode || '91'));
            }, 150);
            
            setupEventListeners(input, iti, selector);
        }).catch(error => {
            console.error('❌ IntlTelInput initialization error:', error);
        });

        return iti;
    };

    function setupEventListeners(input, iti, selector) {
        const errorElement = input.parentElement.querySelector('.phone-error');

        input.addEventListener("blur", () => validatePhoneInput(input, iti, errorElement));
        
        input.addEventListener("input", () => {
            clearPhoneError(input, errorElement);
            if (window.contactFormValidator) {
                window.contactFormValidator.validateField(selector);
            }
        });

        input.addEventListener("countrychange", () => {
            const selectedCountry = iti.getSelectedCountryData();
            const countryName = selectedCountry.name || 
                              (selectedCountry.iso2 ? selectedCountry.iso2.toUpperCase() : '') || 
                              'Unknown';
            console.log('🌍 Country changed to:', countryName, "| Dial Code: +" + selectedCountry.dialCode);
            
            if (input.value.trim()) {
                validatePhoneInput(input, iti, errorElement);
            }
            if (window.contactFormValidator) {
                window.contactFormValidator.validateField(selector);
            }
        });
    }

    function validatePhoneInput(input, iti, errorElement) {
        if (!input.value.trim() && !input.hasAttribute('required')) {
            clearPhoneError(input, errorElement);
            return true;
        }

        if (!input.value.trim() && input.hasAttribute('required')) {
            showPhoneError(input, errorElement, "Phone number is required");
            return false;
        }

        if (!iti.isValidNumber()) {
            const errorMessages = {
                0: "Invalid phone number",
                1: "Invalid country code",
                2: "Phone number is too short",
                3: "Phone number is too long",
                4: "Invalid phone number"
            };
            showPhoneError(input, errorElement, errorMessages[iti.getValidationError()] || "Invalid phone number");
            return false;
        }

        clearPhoneError(input, errorElement);
        input.classList.add('is-valid');
        
        const hiddenField = document.querySelector(`#${input.id}_full`);
        if (hiddenField) hiddenField.value = iti.getNumber();

        return true;
    }

    function showPhoneError(input, errorElement, message) {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
    }

    function clearPhoneError(input, errorElement) {
        input.classList.remove('is-invalid', 'is-valid');
        if (errorElement) errorElement.classList.remove('show');
    }

    window.getPhoneNumber = function(selector = "#phone") {
        const iti = window._phoneInputs[selector];
        if (!iti) return null;
        
        const selectedCountry = iti.getSelectedCountryData();
        
        return {
            number: iti.getNumber(),
            isValid: iti.isValidNumber(),
            country: selectedCountry,
            countryCode: selectedCountry.dialCode,
            countryIso: selectedCountry.iso2,
            countryName: selectedCountry.name,
            e164: iti.getNumber()
        };
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-phone-input]').forEach(input => {
            loadPhoneInput(`#${input.id}`);
        });
    });

    if (typeof window.Livewire !== 'undefined') {
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', () => {
                document.querySelectorAll('[data-phone-input]').forEach(input => {
                    if (input.dataset.itiInitialized !== "true") {
                        loadPhoneInput(`#${input.id}`);
                    }
                });
            });
        });
    }

    console.log('✓ International Phone Input Manager Loaded');
})();
</script>
@endonce