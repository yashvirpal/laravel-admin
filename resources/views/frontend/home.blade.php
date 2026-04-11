@extends('layouts.frontend')


@section('meta')
    {{-- <x-frontend-meta :model="$page" /> --}}
@endsection

@section('content')
    @if ($sliders->isNotEmpty())
        <!--  silder section start here -->
        <section class="slider-home-sec">
            <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">

                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="0" class="active"
                        aria-current="true" aria-label="Slide 1"></button>
                    <button type="button btn--lg" data-bs-target="#carouselExampleControls" data-bs-slide-to="1"
                        aria-label="Slide 2"></button>
                </div>

                <div class="carousel-inner">
                    @foreach ($sliders as $key => $item)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            @php
                                $imgurl = $item->image ? $item->image_url : asset('frontend/images/slider.webp');
                            @endphp
                            <img src="{{ $imgurl }}" class="d-block w-100" alt="{{ $item->image_alt ?? $item->title }}">
                            <div class="carousel-caption">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h2>{{ $item->title ?? "" }} </h2>
                                        <h3>{{ $item->subtitle ?? "" }} </h3>
                                        @if($item->button_text)
                                            <a class="btn mybtn" href="{{ $item->button_link ?? "" }}">
                                                {{ $item->button_text ?? "Call Us Now" }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <!--  silder section end here -->
    @endif
    @if ($whyChooseSections->isNotEmpty())
        <!-- Guarantee  section start here -->
        <section class="guarantee-sec">
            <div class="container">
                <div class="row">
                    @foreach ($whyChooseSections as $key => $item)
                        <x-frontend.why-choose :item="$item" />
                    @endforeach
                </div>
            </div>
        </section>
        <!-- Guarantee  section end here -->
    @endif
    @if ($featuredCategories->isNotEmpty())
        <!-- product category section start here -->
        <section class="product-category-sec">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="product-category-wrap">
                            <div class="owl-carousel owl-theme category">
                                @foreach ($featuredCategories as $item)
                                    <x-frontend.title-image-carousel :item="$item" />
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- product category section end here -->
    @endif
    @if ($popularProducts->isNotEmpty())
        <!-- Bracelets section start here -->
        <section class="bracelets-sec">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="section-title text-center">Popular Products</h2>
                        <div class="bracelets-box">
                            <div class="owl-carousel products-silder owl-theme">
                                @foreach ($popularProducts as $item)
                                    <x-frontend.product-card-carousel :item="$item" />
                                @endforeach
                            </div>
                        </div>
                        <a class="all-products" href="{{ route('page', 'shop') }}">View all products</a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Bracelets section end here -->
    @endif

    @if ($braceletProducts->isNotEmpty())
        <!-- product category section start here -->
        <section class="product-category-sec">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="product-category-wrap">
                            <h2 class="section-title text-center">Shop By Bracelets</h2>
                            <div class="owl-carousel owl-theme category">
                                @foreach ($braceletProducts as $item)
                                    <x-frontend.title-image-carousel :item="$item" />
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- product category section end here -->
    @endif
    @if ($newProducts->isNotEmpty())
        <!-- Bracelets section start here -->
        <section class="bracelets-sec">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="section-title text-center">New Arrivals</h2>
                        <div class="bracelets-box">
                            <div class="owl-carousel products-silder owl-theme">
                                @foreach ($newProducts as $item)
                                    <x-frontend.product-card-carousel :item="$item" />
                                @endforeach
                            </div>
                        </div>
                        <a class="all-products" href="{{ route('page', 'shop') }}">View all products</a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Bracelets section end here -->
    @endif

    @if ($globalSectionFirst)
        <x-frontend.global-section :item="$globalSectionFirst" />
    @endif

    @if ($customizeBracelet)
        <!-- customised bracelets section strat here -->
        <section class="customised-bracelets-sec"
            style="background: url({{ asset('frontend/assets/images/bg4.png') }}) no-repeat center;">
            <div class="container">
                <div class="row">
                    <div class="col-md-9 mx-auto">
                        <h2 class="section-title text-center">{{-- $customizeBracelet->title --}}Special Product</h2>
                        <div class="row">
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <div class="customised-img-box">
                                    <div class="owl-carousel owl-theme" id="customised-img">
                                        <x-frontend.image-carousel :item="$customizeBracelet" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex flex-column justify-content-center">

                                <div class="customised-form">
                                    <h3 class="h3 fw-bold">{{ $customizeBracelet->title }}</h3>
                                    <p>{{ $customizeBracelet->short_description }}</p>
                                    <div class="h4 fw-bold my-2">
                                        <x-frontend.price :item="$customizeBracelet" />
                                    </div>
                                    @if ($customizeBracelet->tags->isNotEmpty())
                                        <div class="custom_tag_pdp my-2">
                                            <ul><strong>Tags:</strong>
                                                @foreach ($customizeBracelet->tags as $tag)
                                                    <li class="tags h5 fw-bold">{{ $tag->title }}@if(!$loop->last),@endif</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <div class="product-rating h6">
                                        <div class="rating-starss d-inline-flex flex-row">
                                            @php
                                                $rating = min(5, max(0, $customizeBracelet->avg_rating ?? 0));
                                                $full = (int) floor($rating);
                                                $half = (($rating - $full) >= 0.5) ? 1 : 0;
                                                $empty = 5 - $full - $half;
                                            @endphp

                                            @for($i = 0; $i < $full; $i++)
                                                <i class="fa fa-star text-warning"></i>
                                            @endfor

                                            @if($half)
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                            @endif

                                            @for($i = 0; $i < $empty; $i++)
                                                <i class="fa fa-star text-secondary"></i>
                                            @endfor
                                        </div>
                                        <span class="rating-count">({{ $customizeBracelet->reviews->count() }} Reviews)</span>
                                    </div>
                                    <div class="mt-4">

                                        <a href="{{ route('products.details', $customizeBracelet->slug) }}"
                                            class="btnn btn-outline-darkk mybtngreen">
                                            Customize Now
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- customised bracelets section strat here -->
    @endif

    @if ($globalSectionSecond)
        <x-frontend.global-section :item="$globalSectionSecond" />
    @endif

    <!-- Instagram Feed section start here -->
    <section class="instagram-feed-sec">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="section-title text-center">Follow Us on Instagram</h2>
                    <div class="instagram-feed-wrap">
                        <div class="owl-carousel owl-theme" id="instagram">
                            <div class="item">
                                <div class="instagram-box">
                                    <figure> <img src="{{ asset('frontend/assets/images/ins1.jpg') }}" alt="Instagram Feed">
                                    </figure>
                                    <a href="#"> <i class="fab fa-instagram"></i> </a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="instagram-box">
                                    <figure> <img src="{{ asset('frontend/assets/images/ins2.jpg') }}" alt="Instagram Feed">
                                    </figure>
                                    <a href="#"> <i class="fab fa-instagram"></i> </a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="instagram-box">
                                    <figure> <img src="{{ asset('frontend/assets/images/ins3.jpg') }}" alt="Instagram Feed">
                                    </figure>
                                    <a href="#"> <i class="fab fa-instagram"></i> </a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="instagram-box">
                                    <figure> <img src="{{ asset('frontend/assets/images/ins4.jpg') }}" alt="Instagram Feed">
                                    </figure>
                                    <a href="#"> <i class="fab fa-instagram"></i> </a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="instagram-box">
                                    <figure> <img src="{{ asset('frontend/assets/images/ins5.jpg') }}" alt="Instagram Feed">
                                    </figure>
                                    <a href="#"> <i class="fab fa-instagram"></i> </a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="instagram-box">
                                    <figure> <img src="{{ asset('frontend/assets/images/ins1.jpg') }}" alt="Instagram Feed">
                                    </figure>
                                    <a href="#"> <i class="fab fa-instagram"></i> </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Instagram Feed section end here -->
@endsection