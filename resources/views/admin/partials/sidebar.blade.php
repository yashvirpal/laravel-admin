<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="./index.html" class="brand-link">
            <img src="{{ asset('backend/assets/img/AdminLTELogo.png') }}" alt="{{ config('app.name') }}"
                class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">{{ config('app.name') }}</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false" id="navigation">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ isActiveRoute('admin.dashboard') }}">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ isActiveRoute('admin.users.*') }}">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Users</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.transactions.index') }}"
                        class="nav-link {{ isActiveRoute('admin.transactions.*')}}">
                        <i class="nav-icon bi bi-currency-dollar"></i>
                        <p>Transactions</p>
                    </a>
                </li>
                <li
                    class="nav-item {{ request()->routeIs('admin.products.*', 'admin.product-categories.*', 'admin.product-tags.*', 'admin.product-attributes.*', 'admin.product-attribute-values.*', 'admin.orders.*', 'admin.coupons.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.ecommerce.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-cart4"></i>
                        <p>E-Commerce <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview ps-3">
                        <li class="nav-item">
                            <a href="{{ route('admin.product-categories.index') }}"
                                class="nav-link {{ isActiveRoute('admin.product-categories.*') }}">
                                <i class="bi bi-boxes"></i>
                                <p>Product Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.product-tags.index') }}"
                                class="nav-link {{ isActiveRoute('admin.product-tags.*') }}">
                                <i class="nav-icon bi bi-tags"></i>
                                <p>Product Tags</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.product-attributes.index') }}"
                                class="nav-link {{ isActiveRoute('admin.product-attribute-values.*') }} {{ isActiveRoute('admin.product-attributes.*') }}">
                                <i class="nav-icon bi bi-tags"></i>
                                <p>Product Attributes</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.products.index') }}"
                                class="nav-link {{ isActiveRoute('admin.products.*') }}">
                                <i class="nav-icon bi bi-box-seam"></i>
                                <p>Products</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.orders.index') }}"
                                class="nav-link {{ isActiveRoute('admin.orders.*') }}">
                                <i class="nav-icon bi bi-bag-check"></i>
                                <p>Orders</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.coupons.index') }}"
                                class="nav-link {{ isActiveRoute('admin.coupons.*')  }}">
                                <i class="nav-icon bi bi-ticket-perforated"></i>
                                <p>Coupon</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li
                    class="nav-item {{ request()->routeIs('admin.blog-posts.*', 'admin.blog-categories.*', 'admin.blog-tags.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-pencil-square"></i>
                        <p>Blogs <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview ps-3">
                        <li class="nav-item">
                            <a href="{{ route('admin.blog-categories.index') }}"
                                class="nav-link {{ isActiveRoute('admin.blog-categories.*') }}">
                                <i class="nav-icon bi bi-folder2-open"></i>
                                <p>Blog Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.blog-tags.index') }}"
                                class="nav-link {{ isActiveRoute('admin.blog-tags.*') }}">
                                <i class="nav-icon bi bi-tags"></i>
                                <p>Blog Tags</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.blog-posts.index') }}"
                                class="nav-link {{ isActiveRoute('admin.blog-posts.*') }}">
                                <i class="nav-icon bi bi-file-earmark-text"></i>
                                <p>Posts</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{ route('admin.calculators.index') }}"
                        class="nav-link {{ isActiveRoute('admin.calculators.*') }}">
                        <i class="nav-icon bi bi-calculator"></i>
                        <p>Remedy Calculators</p>
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a href="{{ route('admin.pages.index') }}" class="nav-link {{ isActiveRoute('admin.pages.*') }}">
                        <i class="nav-icon bi bi-file-text"></i>
                        <p>Pages</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.testimonials.index') }}"
                        class="nav-link {{ isActiveRoute('admin.testimonials.*') }}">
                        <i class="nav-icon bi bi-chat-quote"></i>
                        <p>Testimonials</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.sliders.index') }}"
                        class="nav-link {{ isActiveRoute('admin.sliders.*') }}">
                        <i class="nav-icon bi bi-images"></i>
                        <p>Sliders</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.global-sections.index') }}"
                        class="nav-link {{ isActiveRoute('admin.global-sections.*') }}">
                        <i class="nav-icon bi bi-layout-text-window-reverse"></i>
                        <p>Global Sections</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.settings.index') }}"
                        class="nav-link {{ isActiveRoute('admin.settings.*') }}">
                        <i class="nav-icon bi bi-gear"></i>
                        <p>Settings</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
<!--end::Sidebar-->