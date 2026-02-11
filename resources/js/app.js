import './bootstrap';
import 'intl-tel-input/build/css/intlTelInput.css';
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/js/utils.js';

import {
    showLoader,
    hideLoader,
    restrictInputByType,
    initFormValidator,
    ValidationRules
} from './plugins/global-config';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Make globally available
window.restrictInputByType = restrictInputByType;
window.initFormValidator = initFormValidator;
window.ValidationRules = ValidationRules;
window.showLoader = showLoader;
window.hideLoader = hideLoader;

document.addEventListener("DOMContentLoaded", () => {

    // Example restrictions
    //restrictInputByType(".phone", "number", 7, 15);
    restrictInputByType(".name", "name", 3, 25);

});



import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

// Optional configuration
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
};
window.toastr = toastr;

//toastr.success('Hello! This is a success toast');
//toastr.error('Oops! Something went wrong.');





Alpine.start();
