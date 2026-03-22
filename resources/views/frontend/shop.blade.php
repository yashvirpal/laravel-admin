@extends('layouts.frontend')


@section('meta')
    <x-frontend.meta :model="$page" />
@endsection

@section('content')
    <!-- All products section start here -->
    <section class="all-products-sec">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="top-bar">
                        <div class="top-baar-right">
                            <x-frontend.sorting-shop />
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <x-frontend.shop-sidebar :filters="$filters" />
                </div>

                <div class="col-md-9">

                    <div id="product-list" class="row gy-4 gx-3"></div>

                    <div class="text-center mt-3">
                        <button id="load-more-btn" class="btn btn-outline-primary d-none mybtn">
                            Load More
                        </button>
                    </div>

                </div>
            </div>
        </div>
        </div>
    </section>
    <!-- All products section end here -->
@endsection
@push('scripts')
    <x-frontend.ajax-product-list />
    {{--
    <script>
        let currentPage = 1;

        const productList = document.getElementById('product-list');
        const loadMoreBtn = document.getElementById('load-more-btn');

        function loadProducts(append = false) {

            const categories = Array.from(
                document.querySelectorAll('.category-filter:checked')
            ).map(el => el.value);

            const rating = document.querySelector('.rating-filter:checked')?.value ?? null;

            fetch("{{ route('products.load') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    page: currentPage,
                    categories,
                    rating,
                    min_price: document.getElementById('minPriceInput').value,
                    max_price: document.getElementById('maxPriceInput').value,
                    sort: document.getElementById('sortBy').value
                })
            })
                .then(res => res.json())
                .then(data => {

                    if (append) {
                        productList.insertAdjacentHTML('beforeend', data.html);
                    } else {
                        productList.innerHTML = data.html;
                    }

                    loadMoreBtn.classList.toggle('d-none', !data.hasMore);
                });
        }

        // 🔄 Load more click
        loadMoreBtn.addEventListener('click', () => {
            currentPage++;
            loadProducts(true);
        });

        // 🔁 Filters change → RESET pagination
        document.querySelectorAll(
            '.category-filter, .rating-filter, #sortBy, #minPriceInput, #maxPriceInput'
        ).forEach(el => {
            el.addEventListener('change', () => {
                currentPage = 1;
                loadProducts(false);
            });
        });

        // 🚀 Initial load
        loadProducts();
    </script> --}}


@endpush