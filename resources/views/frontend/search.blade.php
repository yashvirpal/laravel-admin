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
                    <div class="row gy-4 gx-3">
                        @if($products->count() > 0)
                            @foreach ($products as $product)
                                <x-frontend.product-card :item="$product" />
                            @endforeach
                        @else
                            <div class="col-12 text-center py-5">
                                <x-frontend.no-product />
                            </div>
                        @endif
                    </div>
                    <nav aria-label="Page navigation" class="mt-4">
                        <div class="d-flex justify-content-center">
                            {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </nav>
                </div>
            </div>
        </div>
        </div>
    </section>
    <!-- All products section end here -->
@endsection