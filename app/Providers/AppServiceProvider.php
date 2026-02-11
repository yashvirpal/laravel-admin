<?php

namespace App\Providers;

use App\Services\BreadcrumbService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use App\Models\ProductCategory;
use App\Models\Page;
use App\Models\Product;

use Illuminate\Foundation\AliasLoader;
use App\Facades\CartServiceFacade ;
use App\Facades\WishlistServiceFacade ;

use App\Services\CartService;
use App\Services\WishlistService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton('cart', function ($app) {
            return new CartService();
        });

        $this->app->singleton('wishlist', function ($app) {
            return new WishlistService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('CartFacade', CartServiceFacade::class);
        $loader->alias('WishlistFacade', WishlistServiceFacade::class);

        View::composer('*', function ($view) {
            // Mega menu parents
            $megaCategoriesHeaderMenu = ProductCategory::whereNull('parent_id')
                ->whereIn('slug', ['shop-by-concern', 'shop-by-zodiac'])
                ->where('status', 1)
                ->with('children')
                ->get();

            // Direct categories (single links)
            $directCategoriesHeaderMenu = ProductCategory::whereNull('parent_id')
                ->whereNotIn('slug', ['shop-by-concern', 'shop-by-zodiac', 'corporate-gifts', 'puja-needs'])
                ->where('status', 1)
                ->get();

            $categoryHeaderMenu = ProductCategory::whereIn('slug', ['corporate-gifts', 'puja-needs'])
                ->where('status', 1)
                ->get();

            $ourProductsFooterMenu = ProductCategory::whereNull('parent_id')
                ->where('status', 1)
                ->get();
            $quickLinksFooterMenu = Page::whereIn('slug', ['about-us', 'contact-us', 'refund-returns', 'privacy-policy', 'shipping-policy', 'terms-conditions','bulk-enquiry'])
                ->where('status', 1)
                ->with('children')
                ->get();


            $view->with([
                'megaCategoriesHeaderMenu' => $megaCategoriesHeaderMenu,
                'directCategoriesHeaderMenu' => $directCategoriesHeaderMenu,
                'categoryHeaderMenu' => $categoryHeaderMenu,
                'ourProductsFooterMenu' => $ourProductsFooterMenu,
                'quickLinksFooterMenu' => $quickLinksFooterMenu,
                'breadcrumbs' => BreadcrumbService::generate(),
            ]);
            //  $view->with('breadcrumbs', BreadcrumbService::generate());
        });
    }
}
