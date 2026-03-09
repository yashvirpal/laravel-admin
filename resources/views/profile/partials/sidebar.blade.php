<!-- Sidebar -->
<div class="col-lg-3 col-md-4">
    <div class="sidebar">
        <div class="user-profile">
            @if(auth()->user()->profile_image)
                <img src="{{ image_url('author', auth()->user()->profile_image, 'medium') }}"
                    alt="{{ auth()->user()->name }}" loading="lazy"
                    class="w-20 h-20 rounded-full object-cover shadow blur-sm transition-all duration-300"
                    onload="this.classList.remove('blur-sm')">
            @else
                @php
                    $initials = collect(explode(' ', auth()->user()->name))
                        ->map(fn($w) => strtoupper($w[0]))
                        ->take(2)
                        ->join('');
                @endphp
                <div class="user-avatar"> {{ $initials }}</div>
            @endif
            <h5 class="user-name">{{ auth()->user()->name }}</h5>
        </div>
        <ul class="nav-menu">
            <li>
                <a href="{{ route('profile.dashboard') }}"
                    class="{{ request()->routeIs('profile.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="fas fa-user"></i> My profile
                </a>
            </li>
            <li>
                <a href="{{ route('profile.addresses') }}"
                    class="{{ request()->routeIs('profile.addresses*') ? 'active' : '' }}">
                    <i class="fas fa-address-card"></i> Addresses
                </a>
            </li>
            <li>
                <a href="{{ route('profile.orders') }}"
                    class="{{ request()->routeIs('profile.orders*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i> My orders
                </a>
            </li>
            <li>
                <a href="{{ route('profile.wishlist') }}"
                    class="{{ request()->routeIs('profile.wishlist*') ? 'active' : '' }}">
                    <i class="fas fa-heart"></i>My Wishlishts
                </a>
            </li>
            <li>
                <a href="{{ route('profile.dashboard') }}"
                    class="{{ request()->routeIs('profile.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-award"></i> Membership
                </a>
            </li>
            <li>
                <a href="{{ route('profile.password.edit') }}"
                    class="{{ request()->routeIs('profile.password.edit*') ? 'active' : '' }}">
                    <i class="fas fa-key"></i> Change password
                </a>
            </li>
            <li>
                <form id="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf

                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </form>
            </li>

            <!-- <li>
                <a href="#" class="active" onclick="showPage('home')"><i class="fas fa-home"></i> Home</a>
            </li>
            <li>
                <a href="#" onclick="showPage('profile')"><i class="fas fa-user"></i> My profile</a>
            </li>
            <li>
                <a href="#" onclick="showPage('orders')"><i class="fas fa-shopping-bag"></i> My orders</a>
            </li>
            <li>
                <a href="#" onclick="showPage('review')"><i class="fas fa-star"></i> Product review</a>
            </li>
            <li>
                <a href="#" onclick="showPage('recommendations')"><i class="fas fa-eye"></i> Product recommendations</a>
            </li>
            <li>
                <a href="#" onclick="showPage('membership')"><i class="fas fa-award"></i> Membership</a>
            </li>
            <li>
                <a href="#" onclick="showPage('password')"><i class="fas fa-key"></i> Change password</a>
            </li>
            <li><a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li> -->
        </ul>
    </div>
</div>