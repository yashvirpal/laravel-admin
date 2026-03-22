<!-- footer start -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="footer-wrap">
                    <div class="footer-box contact-info">
                        <h3>Contact Us</h3>
                        <ul>
                            <li> <i class="fas fa-map-marker-alt"></i> <span>Diksha Mehta, 23b/5 New Rohtak Road,
                                    Near Liberty Cinema, Dev Nagar Karol Bagh, New Delhi 110005 Opp. Bikaner.</span>
                            </li>
                            <li> <i class="fas fa-phone-alt"></i> <a href="tel:+91 99115 73173"> +91 99115 73173</a>
                            </li>
                            <li> <i class="fas fa-envelope-open-text"></i> <a href="mailto:jovialvision04@gmail.com">
                                    jovialvision04@gmail.com</a></li>
                        </ul>
                        <div class="footer-social-icon">
                            <ul>
                                <li> <a href="https://www.instagram.com/jovialvision/" target="_blank"><i
                                            class="fab fa-instagram"></i></a> </li>
                                <li> <a href="https://www.youtube.com/@jovial_vision" target="_blank"><i
                                            class="fab fa-youtube"></i></a> </li>
                                <li> <a href="https://x.com/jovialvision?s=21" target="_blank"><svg id="Capa_1"
                                            enable-background="new 0 0 1226.37 1226.37" viewBox="0 0 1226.37 1226.37"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="m727.348 519.284 446.727-519.284h-105.86l-387.893 450.887-309.809-450.887h-357.328l468.492 681.821-468.492 544.549h105.866l409.625-476.152 327.181 476.152h357.328l-485.863-707.086zm-144.998 168.544-47.468-67.894-377.686-540.24h162.604l304.797 435.991 47.468 67.894 396.2 566.721h-162.604l-323.311-462.446z" />
                                            <g />
                                            <g />
                                            <g />
                                            <g />
                                            <g />
                                            <g />
                                            <g />
                                            <g />
                                            <g />
                                            <g />
                                            <g />
                                            <g />
                                            <g />
                                            <g />
                                            <g />
                                        </svg></a> </li>
                                <li> <a href="https://www.facebook.com/jovialvision4" target="_blank"><i
                                            class="fab fa-facebook-f"></i></a> </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 col-sm-6">
                <div class="footer-wrap">
                    <div class="footer-box">
                        <h3>Our Products</h3>
                        <ul class="list-unstyled row">
                            @foreach($ourProductsFooterMenu as $category)
                                <li class="col-12">
                                    <a href="{{ route('products.list', $category->slug) }}">
                                        <i class="fas fa-angle-double-right"></i> {{ $category->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-3 col-sm-6">
                <div class="footer-wrap">
                    <div class="footer-box">
                        <h3><!--Links-->&nbsp;</h3>
                        <ul>
                            <li><a href="#"><i class="fas fa-angle-double-right"></i> Shop By Concern</a></li>
                            <li><a href="#"><i class="fas fa-angle-double-right"></i> Shop by Zodiac</a></li>
                            <li><a href="#"><i class="fas fa-angle-double-right"></i> Customised Bracelets</a></li>
                            <li><a href="#"><i class="fas fa-angle-double-right"></i> Corporate Gifts</a></li>
                            <li><a href="#"><i class="fas fa-angle-double-right"></i> Pooja Needs</a></li>
                            <li><a href="#"><i class="fas fa-angle-double-right"></i> Bulk Orders</a></li>
                        </ul>
                    </div>
                </div>
            </div> --}}
            <div class="col-md-3 col-sm-6">
                <div class="footer-wrap">
                    <div class="footer-box">
                        <h3>Quick Link</h3>
                        <ul>
                            @foreach($quickLinksFooterMenu as $menu)
                                <li>
                                    <a href="{{ route('page', $menu->slug) }}">
                                        <i class="fas fa-angle-double-right"></i> {{ $menu->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</footer>
<!-- footer end -->

<!-- copy right section start here -->
<section class="copy-right-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="copy-right-wrap">
                    <p>Copyright © 2025. <a href="#">Jovial Vision </a> All rights reserved.</p>
                    <figure>
                        <img src="{{ asset('frontend/assets/images/payment.webp') }}">
                    </figure>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- copy right section end here -->