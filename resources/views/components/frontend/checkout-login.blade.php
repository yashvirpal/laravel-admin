@guest
    <!-- Returning Customer -->
    <div class="notification-card">
        <div class="toggle-section" onclick="toggleContent('loginContent','loginArrow')">
            <i class="fas fa-user-circle"></i>
            <span>Returning customer? Click here to login</span>
            <i class="fas fa-chevron-down arrow-icon" id="loginArrow"></i>
        </div>

        <div id="loginContent" class="expandable-content">
            <!-- <p style="color:#666;margin-bottom:20px;">
                If you didn't log in, please log in first.
            </p> -->

            <form action="{{ route('checkout.login') }}" id="checkoutLoginForm" method="post"
                class="d-flex flex-wrap justify-between align-items-center gap-2">
                <div class="input-field">
                    <label>Email</label>
                    <input type="email" name="email" id="login_email" placeholder="Enter your email">
                    <small class="text-danger error_email error"></small>
                </div>

                <div class="input-field">
                    <label>Password</label>
                    <input type="password" name="password" id="login_password" placeholder="Enter your password">
                    <small class="text-danger error_password error"></small>
                </div>
                <div class="">
                    <button type="submit" class="submit-buttonn btn mybtn">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </div>
            </form>
          
        </div>
    </div>
@endguest