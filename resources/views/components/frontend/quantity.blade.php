@php
    $disabled = false;
    if ($isSingle) {
        $disabled = ($cartQty ?? 0) > 0;
    }
@endphp

<div class="input-group quantity-group qty-wrapper" id="qtywrapper{{ $product->id }}">
    <button class="btn btn-outline-secondary qty-btn" data-type="minus" type="button" {{ $disabled ? 'disabled' : '' }}>
        −
    </button>

    <input type="number" class="form-control text-center qty-input" value="{{ max(1, (int) $cartQty) }}" min="1"
        data-product-id="{{ $product->id }}" {{ $disabled ? 'disabled' : '' }}
        title="{{ $disabled ? 'Quantity locked — already in cart' : '' }}">

    <button class="btn btn-outline-secondary qty-btn" data-type="plus" type="button" {{ $disabled ? 'disabled' : '' }}>
        +
    </button>
</div>


{{-- @if ($isSingle)
<div class="input-group quantity-group qty-wrapper">
    <button class="btn btn-outline-secondary qty-btn" data-type="minus" type="button">−</button>
    <input type="number" class="form-control text-center qty-input" value="{{ max(1, (int) $cartQty) }}" min="1">
    <button class="btn btn-outline-secondary qty-btn" data-type="plus" type="button">+</button>
</div>
@else
<div class="input-group input-group-sm qty-wrapper mx-auto">
    <button type="button" class="btn btn-outline-secondary qty-btn" data-type="minus">
        <i class="fas fa-minus"></i>
    </button>
    <input type="text" class="form-control text-center qty-input" value="{{ max(1, (int) $cartQty) }}"
        data-product-id="{{ $productId }}" min="1" max="100" readonly />
    <button type="button" class="btn btn-outline-secondary qty-btn" data-type="plus">
        <i class="fas fa-plus"></i>
    </button>
</div>
@endif --}}