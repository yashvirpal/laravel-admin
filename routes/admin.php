<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductTagController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Admin\ProductAttributeValueController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\TransactionController;

use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogTagController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\CalculatorController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GlobalSectionController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;



Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('admin.guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    });

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Route::get('/dashboard', function () {
        //     return view('admin.dashboard');
        // })->name('dashboard');

        // 🧍‍♂️ Admin Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // 🚪 Logout
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::resource('users', UserController::class);
        Route::resource('users.addresses', AddressController::class)->shallow();




        // Categories
        Route::resource('product-categories', ProductCategoryController::class);

        // Tags
        Route::resource('product-tags', ProductTagController::class);

        // Products
        Route::resource('products', ProductController::class);

        Route::resource('product-attributes', ProductAttributeController::class);

        Route::prefix('product-attributes/{product_attribute}')->group(function () {
            Route::resource('values', ProductAttributeValueController::class)->names('product-attribute-values');
        });

        // Orders
        Route::resource('orders', OrderController::class);

        // Coupons
        Route::resource('coupons', CouponController::class);

        // Invoices
        Route::get('transactions/{transaction}/invoice', [TransactionController::class, 'invoice'])->name('transactions.invoice');
        Route::resource('transactions', TransactionController::class);

        Route::resource('blog-categories', BlogCategoryController::class);
        Route::resource('blog-tags', BlogTagController::class);
        Route::resource('blog-posts', BlogPostController::class);

        Route::resource('pages', PageController::class);
        Route::resource('calculators', CalculatorController::class);
        Route::resource('testimonials', TestimonialController::class);
        Route::resource('sliders', SliderController::class);
        Route::resource('global-sections', GlobalSectionController::class);


        //Route::resource('settings', SettingController::class);
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
