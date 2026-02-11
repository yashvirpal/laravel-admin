<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('meta')
    <link rel="icon" href="{{ asset('frontend/assets/images/fevicion.webp') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" as="style" href="{{ asset('frontend/assets/css/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/animate.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/hover-min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/glightbox.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/magnific-popup.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/all.min.css') }}">

    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    @vite(['resources/js/app.js'])


    <link rel="stylesheet" as="style" type="text/css" href="{{ asset('frontend/assets/css/style.css') }}" media="all" />
    <link rel="stylesheet" as="style" type="text/css" href="{{ asset('frontend/assets/css/responsive.css') }}"
        media="all" />
    {{-- Stack Style --}}
    @stack('styles')

</head>

<body>
    @include('frontend.partials.header')
    <main class="">
        @if(!request()->routeIs('home'))
            <x-frontend.banner-breadcrumb />
        @endif

        @yield('content')
    </main>

    @include('frontend.partials.footer')
    <div id="ajax-overlay">
        <div class="spinner"></div>
    </div>

    <script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.magnific-popup.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/aos.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom.js') }}"> </script>
    <script src="{{ asset('frontend/assets/js/cart.js') }}"> </script>

    <script>
        window.App = {
            symbol: @json((string) setting('currency_symbol', '₹')),
            routes: {
                cartAdd: @json(route('cart.add', ':id')),
                cartUpdate: @json(route('cart.update', ':id')),
                cartRemove: @json(route('cart.remove', ':id')),
                cartMini: @json(route('cart.mini')),
             
                checkout: @json(route('page', 'checkout')),
                wishlistToggle: @json(route('wishlist.toggle', ':id')),
                wishlistCount: @json(route('wishlist.count')),

            }
        };
    </script>
    {{-- Stack Script
     //  cartProductQty: @json(route('cart.productQty', ':id')),
    
    
    --}}
    @stack('scripts')
</body>

</html>