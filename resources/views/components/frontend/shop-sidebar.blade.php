@php
    $minPrice = $filters['price']['min'] ?? 0;
    $maxPrice = $filters['price']['max'] ?? 0;
@endphp

<div class="card shadow-sm p-3 mb-3">
    <h6 class="fw-semibold mb-3">Price Range</h6>

    <!-- Inputs -->
    <div class="row g-2 mb-3">
        <div class="col">
            <input type="number" id="minPriceInput" class="form-control form-control-sm" value="{{ $minPrice }}"
                min="{{ $minPrice }}" max="{{ $maxPrice }}">
        </div>
        <div class="col">
            <input type="number" id="maxPriceInput" class="form-control form-control-sm" value="{{ $maxPrice }}"
                min="{{ $minPrice }}" max="{{ $maxPrice }}">
        </div>
    </div>

    <!-- Slider -->
    <div class="range-slider">
        <div class="progress"></div>
        <input type="range" id="minRange" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $minPrice }}">
        <input type="range" id="maxRange" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $maxPrice }}">
    </div>

    <small class="text-muted d-block mt-2">
        ₹<span id="minVal">{{ $minPrice }}</span> –
        ₹<span id="maxVal">{{ $maxPrice }}</span>
    </small>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        const minRange = document.getElementById("minRange");
        const maxRange = document.getElementById("maxRange");
        const minPriceInput = document.getElementById("minPriceInput");
        const maxPriceInput = document.getElementById("maxPriceInput");
        const progress = document.querySelector(".range-slider .progress");
        const minVal = document.getElementById("minVal");
        const maxVal = document.getElementById("maxVal");

        if (!minRange || !maxRange) return;

        const minLimit = parseInt(minRange.min);
        const maxLimit = parseInt(maxRange.max);


        let priceTimer; // ⚠️ debounce timer

        function triggerFilter() {
            clearTimeout(priceTimer);
            priceTimer = setTimeout(() => {
                currentPage = 1;       // ⚠️ reset pagination
                loadProducts(false);  // ⚠️ call AJAX
            }, 400);
        }
        function update() {
            let min = parseInt(minRange.value);
            let max = parseInt(maxRange.value);

            // Prevent crossing
            if (min > max) {
                [min, max] = [max, min];
                minRange.value = min;
                maxRange.value = max;
            }

            // Sync inputs
            minPriceInput.value = min;
            maxPriceInput.value = max;

            // Update labels
            minVal.textContent = min;
            maxVal.textContent = max;

            // Progress bar
            progress.style.left = ((min - minLimit) / (maxLimit - minLimit)) * 100 + "%";
            progress.style.right = 100 - ((max - minLimit) / (maxLimit - minLimit)) * 100 + "%";

            triggerFilter();
        }

        // Slider events
        minRange.addEventListener("input", update);
        maxRange.addEventListener("input", update);

        // Input events
        minPriceInput.addEventListener("change", () => {
            minRange.value = Math.max(minLimit, Math.min(minPriceInput.value, maxRange.value));
            update();
        });

        maxPriceInput.addEventListener("change", () => {
            maxRange.value = Math.min(maxLimit, Math.max(maxPriceInput.value, minRange.value));
            update();
        });

        update();
    });
</script>

<style>
    .range-slider {
        position: relative;
        height: 6px;
        background: #dee2e6;
        border-radius: 5px;
    }

    .range-slider .progress {
        position: absolute;
        height: 100%;
        background: #0d6efd;
        border-radius: 5px;
        left: 0;
        right: 0;
    }

    .range-slider input[type="range"] {
        position: absolute;
        width: 100%;
        top: -6px;
        pointer-events: none;
        background: none;
        -webkit-appearance: none;
    }

    .range-slider input[type="range"]::-webkit-slider-thumb {
        pointer-events: auto;
        width: 16px;
        height: 16px;
        background: #0d6efd;
        border-radius: 50%;
        -webkit-appearance: none;
    }

    .range-slider input[type="range"]::-moz-range-thumb {
        pointer-events: auto;
        width: 16px;
        height: 16px;
        background: #0d6efd;
        border-radius: 50%;
    }
</style>
<!-- Category Filter -->
<div class="card shadow-sm mb-3">
    <div class="card-body p-3">
        <h6 class="fw-semibold mb-3">Categories</h6>

        <ul class="list-unstyled small mb-0">

            @forelse($filters['categories'] ?? [] as $parent)

                @php
                    $parentCount = $parent['products_count'] ?? 0;
                    $childCount = collect($parent['children'] ?? [])->sum('products_count');
                @endphp

                @continue(($parentCount + $childCount) == 0)

                {{-- Parent --}}
                <li class="mb-2">

                    <label class="d-flex align-items-center justify-content-between category-item rounded px-2 py-1"
                        for="pc-{{ $parent['id'] }}">
                        <div class="form-check m-0">
                            <input id="pc-{{ $parent['id'] }}" class="form-check-input category-filter me-2" type="checkbox"
                                value="{{ $parent['id'] }}">
                            <span class="fw-semibold">
                                {{ $parent['title'] }}
                            </span>
                        </div>

                        <span class="badge bg-light text-muted">
                            {{ $parentCount }}
                        </span>
                    </label>

                    {{-- Children --}}
                    @if(!empty($parent['children']))
                        <ul class="list-unstyled ms-4 mt-1">

                            @foreach($parent['children'] as $child)

                                @php $childCount = $child['products_count'] ?? 0; @endphp
                                @continue($childCount == 0)

                                <li class="mb-1">
                                    <label
                                        class="d-flex align-items-center justify-content-between category-item child rounded px-2 py-1"
                                        for="pc-{{ $child['id'] }}">
                                        <div class="form-check m-0">
                                            <input id="pc-{{ $child['id'] }}" class="form-check-input category-filter me-2"
                                                type="checkbox" value="{{ $child['id'] }}">
                                            <span>
                                                {{ $child['title'] }}
                                            </span>
                                        </div>

                                        <span class="badge bg-light text-muted">
                                            {{ $childCount }}
                                        </span>
                                    </label>
                                </li>

                            @endforeach
                        </ul>
                    @endif

                </li>

            @empty
                <li class="text-muted">No categories</li>
            @endforelse

        </ul>
    </div>
</div>
<style>
    .category-item {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .category-item:hover {
        background-color: rgba(0, 0, 0, 0.04);
    }

    .category-item.child {
        font-size: 0.95rem;
    }

    .form-check-input {
        cursor: pointer;
    }

    .badge {
        font-weight: 500;
    }
</style>



<!-- <div class="card shadow-sm p-3 mb-3">
    <h6>Rating</h6>

    @for($i = 5; $i >= 1; $i--)
        <div class="form-check">
            <input class="form-check-input rating-filter" type="radio" name="rating" id="rating-{{ $i }}" value="{{ $i }}">
            <label class="form-check-label" for="rating-{{ $i }}">
                {{ $i }} ★ & up
            </label>
        </div>
    @endfor
</div> -->

{{-- <button type="button" class="btn mybtn mb-3 d-block mx-auto">
    Apply Filter
</button> --}}

@php
    $special = $filters['special'] ?? null;
@endphp
<!-- Special Product -->
@if($special)
    <div class="card shadow-sm p-3">
        <h4 class="fw-semibold">Special Product</h4>
        <div class="product-advertisement">
            <figure>
                <a href="{{ route('products.details', $special->slug) }}">
                    <img src="{{  $special->image ? $special->image_url : asset('frontend/images/product.webp')  }}"
                        alt=" {{ $special->name }}">
                </a>
            </figure>
        </div>
        {{-- {{ currencyFormat($special->sale_price ?? $special->regular_price) }} --}}
    </div>
@endif