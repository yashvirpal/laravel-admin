<!-- header section start here -->
<header>
    <div class="top-header-sec">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="top-header-wrap">
                        <div class="top-left">
                            <div class="offer-slider owl-carousel">
                                <div class="item">
                                    <p>Get 30% Off On Selected Items</p>
                                </div>
                                <div class="item">
                                    <p>Free Shipping on Orders Above ₹999</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="middle-header-sec">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="middle-header-wrap">
                        <div class="left-logo">
                            <figure>
                                <a href="{{ route('home') }}">
                                    <img src="{{ asset('frontend/assets/images/logo.webp') }}" alt="logo">
                                </a>
                            </figure>
                        </div>
                        <div class="middle-right">
                            <div class="search-box">
                                <form action="{{ route('page', 'search') }}" method="get">
                                    <div class="input-group">
                                        <input type="text" name="q" placeholder="Search">
                                        <button><i class="fas fa-search"></i></button>
                                    </div>
                                </form>
                            </div>
                            <div class="login-box">
                                <ul>
                                    <li>
                                        <a class="topwish" href="{{ route('page', 'wishlist') }}">
                                            <i class="fa fa-heart hidden-md hidden-lg hidden-sm"></i>
                                        </a>
                                        <span id="wishlistCount">0</span>
                                    </li>
                                    <li>
                                        @if (Auth::check())
                                            <a href="{{ route('profile.dashboard') }}">
                                                <i class="fas fa-user"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('page', 'login') }}">
                                                <i class="fas fa-user"></i>
                                            </a>
                                        @endif
                                    </li>

                                    <li class="dropdown cart-dropdown">
                                        <a href="{{ route('page', 'cart') }}" class="cart-toggle">
                                            <i class="fas fa-shopping-bag"></i>
                                        </a>
                                        <span class="cartCount">0</span>

                                        <div class=" cart-box">
                                            <div class="cart-header">
                                                <span>Shopping Cart</span>
                                                <a href="javascript:void(0)" class="cart-close">×</a>
                                            </div>
                                            <div class="minicart" id="minicart">
                                                <p class="text-center mt-2">Loading...</p>
                                                {{-- <x-frontend.mini-cart :cart="$cart" /> --}}
                                            </div>

                                        </div>

                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="menu-wrapper">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav mx-auto">
                                    <li class="nav-item products-parent position-relative">
                                        <a href="{{ route('page', 'shop') }}" class="products-toggle">
                                            <i class="fas fa-box"></i> Shops
                                            <i class="fas fa-chevron-down mobile-arrow"></i>
                                        </a>

                                        <ul class="products-submenu">
                                            @foreach($directCategoriesHeaderMenu as $category)
                                                <li class="">
                                                    <a href="{{ route('products.list', $category->full_slug) }}">
                                                        {{ $category->title }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>

                                    @foreach($megaCategoriesHeaderMenu as $category)

                                        <li class="nav-item zodiac-parent">
                                            <a href="{{ route('products.list', $category->slug) }}">
                                                @if ($category->id == 1)
                                                    <i class="fas fa-bullseye"></i>
                                                @elseif ($category->id == 8)
                                                    <i class="fas fa-sun"></i>
                                                @endif
                                                {{ $category->title }}
                                                <i class="fas fa-chevron-down mobile-arrow"></i>
                                            </a>

                                            <div class="mega-menu">
                                                <div class="zodiac-grid">
                                                    @foreach($category->children as $child)
                                                        <div class="z-item">
                                                            <a href="{{ route('products.list', $child->full_slug) }}">
                                                                {{-- {{ $child->image }}
                                                                {{ $child->image_url }} --}}
                                                                <img src="{{ $child->image ? $child->image_url : asset('frontend/images/category.webp') }}"
                                                                    alt="{{ $child->image_alt }}">
                                                                <p>{{ $child->title }}</p>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                    @foreach($categoryHeaderMenu as $category)
                                        <li class="nav-item">
                                            <a href="{{ route('products.list', $category->slug) }}">
                                                @if ($category->id == 21)
                                                    <i class="fas fa-gift"></i>
                                                @elseif ($category->id == 22)
                                                    <i class="fas fa-om"></i>
                                                @endif
                                                {{ $category->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                    <li class="nav-item">
                                        <a href="{{ route('products.details', 'customised-bracelets') }}"><i
                                                class="fas fa-ring"></i> Customised Bracelets</a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{ route('products.details', 'membership-plans') }}"><i
                                                class="fas fa-id-card"></i> Membership Plans</a>
                                    </li>

                                </ul>

                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- header section end here -->