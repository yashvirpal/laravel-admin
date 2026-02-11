<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/admin.php';





// Home & search
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

Route::post('/get-variant-price', [HomeController::class, 'getVariantPrice']);


Route::post('/get-variant-price', [HomeController::class, 'getVariantPrice'])->name('product.variant.price');


// Products
Route::prefix('products')->group(function () {
    //http://localhost:8000/products/details/sample-product-1
    // Product details (slug always last) - MUST BE FIRST
    Route::get('details/{slug}', [HomeController::class, 'productDetails'])->name('products.details');

    Route::post('/products/load', [HomeController::class, 'load'])->name('products.load');


    // http://localhost:8000/products/pisces
    // http://localhost:8000/products/shop-by-zodiac/pisces
    // Product list by category/sub-category
    Route::get('{categories?}', [HomeController::class, 'productList'])
        ->where('categories', '.*') // catch nested categories
        ->name('products.list');
});
// Route::prefix('cart')->group(function () {
//     //Route::get('/', [HomeController::class, 'index'])->name('cart.index');

//     Route::post('add/{product}', [CartController::class, 'add'])->name('cart.add');
//     Route::post('update/{product}', [CartController::class, 'update'])->name('cart.update');
//     Route::delete('remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
//     Route::get('mini', [CartController::class, 'mini'])->name('cart.mini');
//     Route::get('/cart/product-qty/{product}', [CartController::class, 'productQty'])->name('cart.productQty');
// });



Route::prefix('cart')->name('cart.')->group(function () {
   // Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/mini', [CartController::class, 'mini'])->name('mini');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::put('/update/{itemId}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{itemId}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    
    Route::post('/coupon/apply', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::delete('/coupon/{couponId}', [CartController::class, 'removeCoupon'])->name('coupon.remove');
});




// Blog
Route::prefix('blog')->group(function () {
    // Blog post details
    Route::get('post/{slug}', [HomeController::class, 'blogDetails'])
        ->name('blog.details');
    // Blog list by category/sub-category
    Route::get('{categories?}', [HomeController::class, 'blogList'])
        ->where('categories', '.*') // catch nested categories
        ->name('blog.list');
});
//Route::post('/products/filter', [ProductController::class, 'filter'])->name('products.filter');
Route::get('/wishlistcount', [WishlistController::class, 'count'])->name('wishlist.count');


Route::post('/checkout/login', [CheckoutController::class, 'login'])->name('checkoutLogin');
Route::post('/checkout/create-order', [CheckoutController::class, 'checkOut'])->name('createOrder');


Route::post('/contact-form-submit', [HomeController::class, 'contactFormSubmit'])->name('contact.submit');
Route::post('/newsletter/subscribe', [HomeController::class, 'newsletterSubscribe'])->name('newsletter.subscribe');
Route::get('/sitemap.xml', [HomeController::class, 'sitemapXML'])->name('sitemapxml');



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// Dynamic pages (keep last)
Route::get('/{slug}', [HomeController::class, 'page'])->name('page');

require __DIR__ . '/auth.php';
