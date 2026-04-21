@extends('layouts.frontend')


@section('meta')
    <x-frontend.meta :model="$page" />
@endsection

@section('content')
    <!--  contact us section start here -->
    <section class="contact-sec">
        <div class="container my-5">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="contact-info-card">
                        <h2 class="section-title">Send a Message</h2>
                        <p class="mb-4">Feel free to contact us JOVIAL VISION. We'd love to hear from you! Whether you have
                            a question, comment, or concern, our team is here to help.</p>

                        @if(setting('phone'))
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-item-content">
                                    <h6>Phone</h6>
                                    <p>
                                        <a href="tel:{{ preg_replace('/\s+/', '', setting('phone')) }}"
                                            class="text-decoration-none text-reset">
                                            {{ setting('phone') }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if(setting('email'))
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-item-content">
                                    <h6>Email</h6>
                                    <p>
                                        <a href="mailto:{{ setting('email') }}" class="text-decoration-none text-reset">
                                            {{ setting('email') }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if(setting('address'))
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-item-content">
                                    <h6>Address</h6>
                                    <p>{!! nl2br(e(str_replace('\n', PHP_EOL, setting('address')))) !!}</p>
                                </div>
                            </div>
                        @endif

                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Follow us on</h6>
                            @php
                                $socialLinks = json_decode(setting('social'), true) ?? [];

                                $platforms = [
                                    'instagram' => [
                                        'icon' => 'fab fa-instagram',
                                        'label' => 'Instagram'
                                    ],
                                    'youtube' => [
                                        'icon' => 'fab fa-youtube',
                                        'label' => 'YouTube'
                                    ],
                                    'twitter' => [
                                        'icon' => 'fab fa-twitter',
                                        'label' => 'X'
                                    ],
                                    'facebook' => [
                                        'icon' => 'fab fa-facebook-f',
                                        'label' => 'Facebook'
                                    ],
                                ];
                            @endphp

                            <div class="social-links">
                                @foreach($platforms as $key => $platform)
                                    @if(!empty($socialLinks[$key]))
                                        <a href="{{ $socialLinks[$key] }}" class="social-link" target="_blank"
                                            rel="noopener noreferrer" aria-label="{{ $platform['label'] }}">
                                            @if ($key == 'twitter')
                                                <svg class="social-svg-icon" viewBox="0 0 1226.37 1226.37"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="m727.348 519.284 446.727-519.284h-105.86l-387.893 450.887-309.809-450.887h-357.328l468.492 681.821-468.492 544.549h105.866l409.625-476.152 327.181 476.152h357.328l-485.863-707.086zm-144.998 168.544-47.468-67.894-377.686-540.24h162.604l304.797 435.991 47.468 67.894 396.2 566.721h-162.604l-323.311-462.446z" />
                                                </svg>
                                                <style>
                                                    .social-svg-icon {
                                                        width: 16px;
                                                        height: 16px;
                                                        display: block;
                                                        margin: auto;
                                                        fill: currentColor;
                                                    }
                                                </style>
                                            @else
                                                <i class="{{ $platform['icon'] }}"></i>
                                            @endif
                                        </a>
                                    @endif
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="form-card">
                        <h2 class="section-title">Get in Touch</h2>
                        <form id="contactForm" method="post" action="{{ route('contact.submit') }}">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Enter your name" name="name"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Enter your email"
                                    name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" placeholder="Enter your phone number"
                                    name="phone">
                            </div>

                            <div class="mb-4">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" rows="5" placeholder="Enter your message"
                                    name="message" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-send">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="map-container">
                @if (setting('map'))
                    <iframe src="{{ setting('map') }}" width="100%" height="450" style="border:0;" allowfullscreen=""
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                @else
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3501.4727688845447!2d77.18396931508236!3d28.644805782420944!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d029c4e8b6c5d%3A0x7e5e5e5e5e5e5e5e!2sNew%20Rohtak%20Rd%2C%20Karol%20Bagh%2C%20New%20Delhi%2C%20Delhi%20110005!5e0!3m2!1sen!2sin!4v1234567890123!5m2!1sen!2sin"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                @endif

            </div>
        </div>
    </section>
    <!-- contact us section end here -->
@endsection

@push('scripts')
    <x-frontend.intl-tel-input />
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            loadPhoneInput("#phone");
            const rules = [
                { selector: "#name", rule: "name" },
                { selector: "#email", rule: "email" },
                { selector: "#phone", rule: "phone" },
                { selector: "#message", rule: "message" }
            ];

            initFormValidator("#contactForm", rules);
        });
    </script>
@endpush