{{-- resources/views/components/frontend/cart-product.blade.php --}}

<tr id="cart-item-{{ $item->id }}" class="{{ $item->price == 0 ? 'table-success' : '' }}">
    <!-- Product -->
    <td>
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $item->product->image ? $item->product->image_url : asset('frontend/images/product.webp') }}"
                alt="{{ $item->product->title }}" width="80" height="80" class="rounded" style="object-fit: cover;">
            <div>
                <h6 class="mb-1">
                    <a href="{{ route('products.details', $item->product->slug) }}" class="text-decoration-none text-dark">
                        {{ $item->product->title }}
                    </a>
                </h6>
                @if($item->variant)
                    <small class="text-muted">{{ $item->variant->name }}</small>
                @endif
                @if($item->price == 0)
                    <span class="badge bg-success">FREE</span>
                @endif
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-outline-danger"
                        onclick="removeFromCart({{ $item->id }})">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    </td>

    <!-- Price -->
    <td>
        @if($item->price == 0)
            <span class="text-success fw-bold">FREE</span>
        @else
            {{ currencyformat($item->price) }}
        @endif
    </td>

    <!-- Quantity -->
    <td class="text-center">
        @if($item->price == 0)
            <!-- Free items can't be edited -->
            <span class="badge bg-light text-dark">{{ $item->quantity }}</span>
        @else
            <div class="qty-wrapper d-inline-flex border rounded">
                <button type="button" class="btn btn-sm qty-btn" data-type="minus" data-item-id="{{ $item->id }}">
                    -
                </button>
                <input type="number" class="form-control form-control-sm text-center border-0 qty-input"
                    value="{{ $item->quantity }}" min="1" max="100" data-item-id="{{ $item->id }}" style="width: 60px;">
                <button type="button" class="btn btn-sm qty-btn" data-type="plus" data-item-id="{{ $item->id }}">
                    +
                </button>
            </div>
        @endif
    </td>

    <!-- Subtotal -->
    <td class="text-end">
        <span class="fw-bold" id="subtotal-{{ $item->id }}">
            @if($item->price == 0)
                <span class="text-success">FREE</span>
            @else
                {{ currencyformat($item->price * $item->quantity) }}
            @endif
        </span>
    </td>
</tr>