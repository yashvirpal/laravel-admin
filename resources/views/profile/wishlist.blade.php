@extends('layouts.frontend')
@section('meta')
    {{-- <x-frontend-meta :model="$page" /> --}}
@endsection

@section('content')
    <section class="account-sec">
        <div class="container mt-4 mb-5">
           
            <div class="row">
                @include('profile.partials.sidebar')

                <!-- Main Content -->
                <div class="col-lg-9 col-md-8">
                    <div class="main-content">
                        <div class="d-flex justify-content-center gap-6 flex-wrap" id="wishlistPage">
                            @if ($wishlists->count() > 0)
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Stock Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse($wishlists as $item)
                                            @php $product = $item->wishlistable; @endphp
                                            <tr class="product-row" id="wishlist-row-{{ $product->id }}">
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="{{ $product->image ?? asset('frontend/assets/images/pro2.jpg') }}"
                                                            alt="{{ $product->title ?? $product->name }}"
                                                            class="rounded-circle border shadow-sm"
                                                            style="width:60px; height:60px; object-fit:cover;">

                                                        <h6 class="product-title mb-0 ms-2">
                                                            <a href="#" class="text-decoration-none text-dark fw-semibold">
                                                                {{ $product->title ?? $product->name }}
                                                            </a>
                                                        </h6>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        @if ($product->sale_price)
                                                            <span class="fw-bold">
                                                                {{ currencyformat($product->sale_price) }}
                                                            </span>
                                                            <small class="text-muted text-decoration-line-through">
                                                                {{ currencyformat($product->regular_price) }}
                                                            </small>
                                                            <span class="badge border border-danger text-danger rounded-pill">
                                                                {{ $product->discountPercentage() }}% OFF
                                                            </span>
                                                        @else
                                                            <span class="fw-bold fs-5 text-dark">
                                                                {{ currencyformat($product->regular_price) }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                </td>

                                                <td>
                                                    @if($product->stock ?? true)
                                                        <span class="badge border border-success text-success rounded-pill">
                                                            <i class="fas fa-check-circle me-1"></i> In Stock
                                                        </span>
                                                    @else
                                                        <span class="badge border border-danger text-danger rounded-pill">
                                                            <i class="fas fa-times-circle me-1"></i> Out of Stock
                                                        </span>
                                                    @endif
                                                </td>

                                                <td class="text-end">
                                                    @if($product->stock ?? true)
                                                        <button class="btn btn-sm btn-outline-danger me-1" title="Add to Cart"
                                                            onclick="addToCart({{ $product->id }})">
                                                            <i class="fas fa-shopping-cart"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary me-1" disabled title="Out of Stock">
                                                            <i class="fas fa-cart-plus"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-sm btn-outline-success me-1"
                                                        onclick="wishlistToggle({{ $product->id }},this)"
                                                        data-id="{{ $product->id }}" data-type="Product"
                                                        title="{{ $product->is_wishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                                                        <i class="fas fa-heart"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <p class="text-muted fs-5 mb-0">
                                                        Your wishlist is empty.
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            @else
                                <x-frontend.no-product />
                            @endif
                        </div>
                        {{-- <div class="col-12 col-md-10 offset-md-1">

                            <div class="row gy-4 gx-3">

                                @forelse($wishlists as $item)
                                @php $product = $item->wishlistable; @endphp
                                <x-frontend.product-card :item="$product" />
                                @empty <div class="col-12 text-center py-5">
                                    <x-frontend.no-product />
                                </div>
                                @endforelse

                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')


@endpush