@extends('layouts.frontend')


@section('meta')
    {{-- <x-frontend-meta :model="$page" /> --}}
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

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-item-content">
                                <h6>Phone</h6>
                                <p>+91 99115 73173</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-item-content">
                                <h6>Email</h6>
                                <p>jovialvision04@gmail.com</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-item-content">
                                <h6>Address</h6>
                                <p>Diksha Mehta, 23b/5 New Rohtak Road, Near Liberty Cinema, Dev Nagar Karol Bagh, New Delhi
                                    110005 Opp. Bikaner.</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Follow us on</h6>
                            <div class="social-links">
                                <a href="#" class="social-link" aria-label="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="social-link" aria-label="YouTube">
                                    <i class="fab fa-youtube"></i>
                                </a>
                                <a href="#" class="social-link" aria-label="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link" aria-label="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
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
                                <input type="text" class="form-control" id="name" placeholder="Enter your name" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Enter your email" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" placeholder="Enter your phone number">
                            </div>

                            <div class="mb-4">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" rows="5" placeholder="Enter your message"
                                    required></textarea>
                            </div>

                            <button type="submit" class="btn btn-send">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3501.4727688845447!2d77.18396931508236!3d28.644805782420944!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d029c4e8b6c5d%3A0x7e5e5e5e5e5e5e5e!2sNew%20Rohtak%20Rd%2C%20Karol%20Bagh%2C%20New%20Delhi%2C%20Delhi%20110005!5e0!3m2!1sen!2sin!4v1234567890123!5m2!1sen!2sin"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
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