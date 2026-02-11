/* Cart & Mini Cart functionality */

/**
 * Debounce function to prevent multiple rapid calls
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Track if an update is in progress
 */
let isUpdating = false;

/**
 * Route helper function
 */
function route(name, id = null) {
    const routes = {
        'cart.index': '/cart',
        'cart.add': '/cart/add/' + id,
        'cart.update': '/cart/update/' + id,
        'cart.remove': '/cart/remove/' + id,
        'cart.clear': '/cart/clear',
        'cart.mini': '/cart/mini',
        'cart.coupon.apply': '/cart/coupon/apply',
        'cart.coupon.remove': '/cart/coupon/' + id,
        'checkout.index': '/checkout',
        'products.index': '/products',
    };
    
    // Fallback to App.routes if available
    if (typeof App !== 'undefined' && App.routes && App.routes[name]) {
        let url = App.routes[name];
        return id ? url.replace(':id', id) : url;
    }
    
    return routes[name] || '/';
}

/**
 * Show loader
 */
function showLoader() {
    const loader = document.getElementById('global-loader');
    if (loader) {
        loader.style.display = 'flex';
    }
}

/**
 * Hide loader
 */
function hideLoader() {
    const loader = document.getElementById('global-loader');
    if (loader) {
        loader.style.display = 'none';
    }
}

/**
 * Add product to cart
 */
function addToCart(productId, qty = 1, buyNow = false, variantId = null) {
    showLoader();
    
    // Get quantity from input if available
    const input = document.querySelector('.qty-input');
    if (input) {
        qty = parseInt(input.value) || 1;
    }

    // Get variant if available
    const variantSelect = document.querySelector('#variant-select');
    if (variantSelect && !variantId) {
        variantId = variantSelect.value || null;
    }

    fetch(route('cart.add', productId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            quantity: qty,
            variant_id: variantId
        })
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    })
    .then(data => {
        if (data.status === false) {
            toastr.error(data.message);
            return;
        }

        // Update cart count
        document.querySelectorAll('.cartCount').forEach(el => {
            el.innerText = data.cart_count;
        });

        // Reload mini cart
        loadMiniCart();

        // Show success message
        toastr.success(data.message);

        // Handle buy now button
        const buyNowBtn = document.querySelector(`#buyNow${productId}`);
        if (buyNowBtn) {
            const addCartBtn = document.querySelector(`#addCart${productId}`);
            if (addCartBtn) {
                addCartBtn.disabled = true;
                addCartBtn.textContent = '✓ Added to Cart';
            }
            buyNowBtn.disabled = true;

            // Disable quantity controls
            const wrapper = document.querySelector(`#qtywrapper${productId}`);
            if (wrapper) {
                wrapper.querySelectorAll("button, input").forEach((el) => {
                    el.disabled = true;
                });
            }
        } else {
            // Disable all add to cart buttons for this product
            const addCartButtons = document.querySelectorAll(`.addCart${productId}`);
            addCartButtons.forEach(function (item) {
                item.disabled = true;
                
                // Update button content
                if (item.querySelector('svg')) {
                    // Has SVG icon
                    const svg = item.querySelector('svg g');
                    if (svg) {
                        svg.setAttribute('fill', '#999');
                    }
                } else {
                    // Text button
                    item.innerHTML = '<i class="bi bi-check-circle me-1"></i>Added';
                }
                
                item.classList.add('in-cart');
                item.setAttribute('title', 'Already in Cart');
            });
        }

        // Redirect to checkout if buy now
        if (buyNow) {
            setTimeout(() => {
                window.location.href = route('checkout.index');
            }, 1500);
        }
    })
    .catch(err => {
        console.error("Add To Cart Error:", err);
        toastr.error(err.message || "⚠️ Oops! Something went wrong. Please try again 😕");
    })
    .finally(hideLoader);
}

/**
 * Add to cart from product listing (simple wrapper)
 */
function addToCartFromListing(productId) {
    addToCart(productId, 1, false, null);
}

/**
 * Add variant product to cart (for single product page)
 */
function addVariantToCart(productId, buyNow = false) {
    // Check if variant is selected (selectedVariantId is set by variant selection JS)
    if (typeof selectedVariantId === 'undefined' || !selectedVariantId) {
        toastr.warning('Please select all product options');
        return;
    }

    const qty = parseInt(document.querySelector('.qty-input')?.value) || 1;
    
    addToCart(productId, qty, buyNow, selectedVariantId);
}

/**
 * Update cart item quantity (internal - with debouncing)
 */
const updateCartInternal = debounce(function(itemId, qty) {
    if (isUpdating) {
        console.log('Update already in progress, skipping...');
        return;
    }

    qty = parseInt(qty);
    
    if (qty < 1) {
        if (!confirm('Remove this item from cart?')) {
            // Reset input to previous value
            const input = document.querySelector(`input[data-item-id="${itemId}"]`);
            if (input && input.dataset.previousValue) {
                input.value = input.dataset.previousValue;
            }
            return;
        }
        removeFromCart(itemId);
        return;
    }

    if (qty > 100) {
        toastr.warning('Maximum quantity is 100');
        const input = document.querySelector(`input[data-item-id="${itemId}"]`);
        if (input) {
            input.value = 100;
            qty = 100;
        }
    }

    isUpdating = true;
    
    // Add visual loading state
    const wrapper = document.querySelector(`input[data-item-id="${itemId}"]`)?.closest('.qty-wrapper');
    if (wrapper) {
        wrapper.classList.add('loading');
        wrapper.querySelectorAll('button, input').forEach(el => el.disabled = true);
    }

    showLoader();

    fetch(route('cart.update', itemId), {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity: qty })
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    })
    .then(data => {
        if (data.status === false) {
            toastr.error(data.message);
            // Reset to previous value
            const input = document.querySelector(`input[data-item-id="${itemId}"]`);
            if (input && input.dataset.previousValue) {
                input.value = input.dataset.previousValue;
            }
            return;
        }

        // Success - no toast to reduce noise
        // toastr.success(data.message);

        // Update the input's previous value
        const input = document.querySelector(`input[data-item-id="${itemId}"]`);
        if (input) {
            input.dataset.previousValue = qty;
        }

        // Update item subtotal
        const itemSubtotalEl = document.querySelector(`#subtotal-${itemId}`);
        if (itemSubtotalEl && data.item_subtotal) {
            itemSubtotalEl.innerText = `${window.symbol || '₹'}${data.item_subtotal}`;
        }

        // Update cart totals
        updateCartTotals(data);

        // Reload mini cart
        loadMiniCart();
    })
    .catch(err => {
        console.error("Update Cart Error:", err);
        toastr.error("⚠️ Failed to update cart");
        
        // Reset to previous value on error
        const input = document.querySelector(`input[data-item-id="${itemId}"]`);
        if (input && input.dataset.previousValue) {
            input.value = input.dataset.previousValue;
        }
    })
    .finally(() => {
        hideLoader();
        isUpdating = false;
        
        // Remove loading state
        if (wrapper) {
            wrapper.classList.remove('loading');
            wrapper.querySelectorAll('button, input').forEach(el => el.disabled = false);
        }
    });
}, 500); // 500ms debounce delay

/**
 * Update cart (public wrapper)
 */
function updateCart(itemId, qty) {
    updateCartInternal(itemId, qty);
}

/**
 * Update cart quantity (wrapper for compatibility)
 */
function updateCartQty(itemId, qty) {
    updateCart(itemId, qty);
}

/**
 * Remove item from cart
 */
function removeFromCart(itemId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }

    showLoader();

    fetch(route('cart.remove', itemId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    })
    .then(data => {
        if (data.status === false) {
            toastr.error(data.message);
            return;
        }

        toastr.success(data.message);

        // Update cart count
        document.querySelectorAll('.cartCount').forEach(el => {
            el.innerText = data.cart_count;
        });

        // Remove item from DOM
        const cartItem = document.getElementById(`cart-item-${itemId}`);
        if (cartItem) {
            cartItem.remove();
        }

        // Check if cart is empty
        if (data.cart_count === 0) {
            const cartPage = document.getElementById('cartpage');
            if (cartPage) {
                cartPage.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x fs-1 text-muted"></i>
                        <p class="text-muted mt-3">Your cart is empty.</p>
                        <a href="${route('products.index')}" class="btn btn-primary mt-2">
                            Continue Shopping
                        </a>
                    </div>
                `;
            }
        } else {
            // Update cart totals
            updateCartTotals(data);
        }

        // Reload mini cart
        loadMiniCart();
    })
    .catch(err => {
        console.error("Remove From Cart Error:", err);
        toastr.error(err.message || "⚠️ Oops! Something went wrong. Please try again 😕");
    })
    .finally(hideLoader);
}

/**
 * Load mini cart
 */
function loadMiniCart() {
    fetch(route('cart.mini'), {
        headers: { 
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    })
    .then(data => {
        if (data.status === false) {
            return; // Silently fail for mini cart
        }

        // Update mini cart HTML
        const miniCartEl = document.getElementById('minicart');
        if (miniCartEl && data.html) {
            miniCartEl.innerHTML = data.html;
        }

        // Update cart count
        document.querySelectorAll('.cartCount').forEach(el => {
            el.innerText = data.cart_count || 0;
        });

        // Update cart total in header if exists
        const cartTotalEl = document.querySelector('.header-cart-total');
        if (cartTotalEl && data.cart_total) {
            cartTotalEl.innerText = `${window.symbol || '₹'}${data.cart_total}`;
        }
    })
    .catch(err => {
        console.error("Mini Cart Error:", err);
        // Silently fail for mini cart - don't show error to user
    });
}

/**
 * Apply coupon to cart
 */
function applyCoupon() {
    const couponInput = document.getElementById('coupon-code');
    if (!couponInput) return;

    const code = couponInput.value.trim().toUpperCase();
    if (!code) {
        toastr.warning('Please enter a coupon code');
        couponInput.focus();
        return;
    }

    showLoader();

    fetch(route('cart.coupon.apply'), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ code: code })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === false) {
            toastr.error(data.message);
            return;
        }

        toastr.success(data.message);

        // Clear input
        couponInput.value = '';

        // Update totals
        updateCartTotals(data);

        // Add coupon badge to applied coupons list
        const appliedCouponsList = document.getElementById('applied-coupons-list');
        if (appliedCouponsList && data.coupon_id) {
            const couponHtml = `
                <div class="d-inline-flex align-items-center bg-success text-white px-3 py-1 rounded me-2 mb-2" 
                     data-coupon-id="${data.coupon_id}">
                    <span class="me-2">${code}</span>
                    <small class="me-2">(-${window.symbol || '₹'}${data.discount})</small>
                    <button type="button" 
                            class="btn-close btn-close-white btn-sm" 
                            onclick="removeCoupon(${data.coupon_id})"
                            aria-label="Remove"></button>
                </div>
            `;
            appliedCouponsList.insertAdjacentHTML('beforeend', couponHtml);
        }

        // Show discount row
        const discountRow = document.getElementById('discount-row');
        if (discountRow) {
            discountRow.style.display = 'table-row';
        }

        // Show free items if any
        if (data.free_items && data.free_items.length > 0) {
            toastr.info(`You've got ${data.free_items.length} free item(s)!`, 'Free Items!');
            
            // Reload page to show free items
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            // Update mini cart
            loadMiniCart();
        }
    })
    .catch(err => {
        console.error("Apply Coupon Error:", err);
        toastr.error("⚠️ Failed to apply coupon. Please try again.");
    })
    .finally(hideLoader);
}

/**
 * Remove coupon from cart
 */
function removeCoupon(couponId) {
    if (!confirm('Remove this coupon?')) {
        return;
    }

    showLoader();

    fetch(route('cart.coupon.remove', couponId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === false) {
            toastr.error(data.message);
            return;
        }

        toastr.success(data.message);

        // Remove coupon badge
        const couponBadge = document.querySelector(`[data-coupon-id="${couponId}"]`);
        if (couponBadge) {
            couponBadge.remove();
        }

        // Check if no more coupons
        const appliedCouponsList = document.getElementById('applied-coupons-list');
        if (appliedCouponsList) {
            const remainingCoupons = appliedCouponsList.querySelectorAll('[data-coupon-id]');
            if (remainingCoupons.length === 0) {
                // Hide discount row
                const discountRow = document.getElementById('discount-row');
                if (discountRow) {
                    discountRow.style.display = 'none';
                }
            }
        }

        // Update totals
        updateCartTotals(data);

        // Reload page to remove free items
        setTimeout(() => {
            location.reload();
        }, 1000);
    })
    .catch(err => {
        console.error("Remove Coupon Error:", err);
        toastr.error("⚠️ Failed to remove coupon. Please try again.");
    })
    .finally(hideLoader);
}

/**
 * Update cart totals on page
 */
function updateCartTotals(data) {
    const symbol = window.symbol || '₹';

    // Update subtotal
    if (data.cart_subtotal !== undefined) {
        const subtotalEl = document.getElementById('cart-subtotal');
        if (subtotalEl) {
            subtotalEl.innerText = `${symbol}${data.cart_subtotal}`;
        }
    }

    // Update discount
    if (data.cart_discount !== undefined) {
        const discountEl = document.getElementById('cart-discount');
        if (discountEl) {
            discountEl.innerText = `-${symbol}${data.cart_discount}`;
            
            // Show/hide discount row
            const discountRow = document.getElementById('discount-row');
            if (discountRow) {
                const discountValue = parseFloat(data.cart_discount.replace(/,/g, ''));
                discountRow.style.display = discountValue > 0 ? 'table-row' : 'none';
            }
        }
    }

    // Update tax
    if (data.cart_tax !== undefined) {
        const taxEl = document.getElementById('cart-tax');
        if (taxEl) {
            taxEl.innerText = `${symbol}${data.cart_tax}`;
        }
    }

    // Update shipping
    if (data.cart_shipping !== undefined) {
        const shippingEl = document.getElementById('cart-shipping');
        if (shippingEl) {
            shippingEl.innerText = `${symbol}${data.cart_shipping}`;
        }
    }
    
    // Update grand total
    if (data.cart_total !== undefined) {
       
        document.querySelectorAll('#cart-total, .cart-total').forEach(el => {
            console.log(el)
            el.innerText = `${symbol}${data.cart_total}`;
        });
    }
}

/**
 * Clear entire cart
 */
function clearCart() {
    if (!confirm('Are you sure you want to clear your entire cart?')) {
        return;
    }

    showLoader();

    fetch(route('cart.clear'), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === false) {
            toastr.error(data.message);
            return;
        }

        toastr.success(data.message);
        setTimeout(() => {
            location.reload();
        }, 1000);
    })
    .catch(err => {
        console.error("Clear Cart Error:", err);
        toastr.error("⚠️ Failed to clear cart. Please try again.");
    })
    .finally(hideLoader);
}

/**
 * Initialize cart on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Load mini cart
    loadMiniCart();

    // Store initial values for all quantity inputs
    document.querySelectorAll('.qty-input[data-item-id]').forEach(input => {
        input.dataset.previousValue = input.value;
    });

    // Auto uppercase coupon code input
    const couponInput = document.getElementById('coupon-code');
    if (couponInput) {
        couponInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        // Allow enter key to apply coupon
        couponInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyCoupon();
            }
        });
    }

    // Auto-apply coupon from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const couponFromUrl = urlParams.get('coupon');
    if (couponFromUrl && couponInput) {
        couponInput.value = couponFromUrl;
        setTimeout(() => applyCoupon(), 500);
    }
});

/**
 * Handle quantity buttons (plus/minus) with debouncing
 */
document.addEventListener("click", function (e) {
    const btn = e.target.closest(".qty-btn");
    if (!btn || isUpdating) return;

    const wrapper = btn.closest(".qty-wrapper");
    if (!wrapper) return;

    const input = wrapper.querySelector(".qty-input");
    if (!input) return;

    const itemId = btn.dataset.itemId || input.dataset.itemId;
    let value = parseInt(input.value) || 1;

    const min = parseInt(input.min) || 1;
    const max = parseInt(input.max) || 100;

    // Store previous value if not already stored
    if (!input.dataset.previousValue) {
        input.dataset.previousValue = value;
    }

    if (btn.dataset.type === "plus" && value < max) {
        value++;
    } else if (btn.dataset.type === "minus" && value > min) {
        value--;
    } else {
        return; // No change needed
    }

    input.value = value;

    // Only update cart if we're on the cart page (has itemId)
    if (itemId) {
        updateCartInternal(itemId, value);
    }
});

/**
 * Handle manual quantity input change with debouncing
 */
document.addEventListener("change", function(e) {
    if (e.target.matches('.qty-input') && !isUpdating) {
        const input = e.target;
        const itemId = input.dataset.itemId;
        
        if (itemId) {
            const value = parseInt(input.value) || 1;
            
            // Store previous value if not already stored
            if (!input.dataset.previousValue) {
                input.dataset.previousValue = value;
            }
            
            updateCartInternal(itemId, value);
        }
    }
});