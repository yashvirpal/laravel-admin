<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Page;

class BreadcrumbService
{
    /**
     * Generate breadcrumbs array.
     * Always returns array (possibly only Home).
     *
     * Each item: ['label' => '...', 'url' => '...']
     */
    public static function generate(): array
    {
        $crumbs = [];

        // Home (always first)
        $crumbs[] = [
            'label' => 'Home',
            'url' => url('/'),
        ];
        if (request()->routeIs('profile*')) {
            $crumbs[] = [
                'label' => 'My Account',
                'url' => url('/'),
            ];
        }

        $route = request()->route();
        $params = $route?->parameters() ?? [];

        // --------------------------
        // Category listing pages
        // e.g. /products/shop-by-zodiac/pisces  (categories param present)
        // --------------------------
        if (isset($params['categories'])) {
            // categories can be nested path — take last segment as the slug
            $parts = explode('/', trim($params['categories'], '/'));
            $slug = end($parts);

            $category = ProductCategory::where('slug', $slug)->first();
            if ($category) {
                // append category ancestors + itself
                foreach ($category->getBreadcrumbs() as $crumb) {
                    $crumbs[] = $crumb;
                }

                return $crumbs;
            }
        }

        // --------------------------
        // Product details page
        // route name products.details (details/{slug})
        // --------------------------
        if (request()->routeIs('products.details') && isset($params['slug'])) {
            $product = Product::with('categories')->where('slug', $params['slug'])->first();

            if ($product) {
                // if product has categories, use first category chain
                if ($product->categories->isNotEmpty()) {
                    $firstCategory = $product->categories->first();
                    foreach ($firstCategory->getBreadcrumbs() as $crumb) {
                        $crumbs[] = $crumb;
                    }
                }

                // finally append product itself
                $crumbs[] = [
                    'label' => $product->title ?? $product->name ?? 'Product',
                    'url' => route('products.details', $product->slug),
                ];
            }

            return $crumbs;
        }

        // --------------------------
        // CMS page (dynamic pages route '/{slug}')
        // route name: page
        // --------------------------
        if (request()->routeIs('page') && isset($params['slug'])) {
            $page = Page::where('slug', $params['slug'])->first();
            if ($page) {
                $crumbs[] = [
                    'label' => $page->title,
                    'url' => url()->current(),
                ];
            }

            return $crumbs;
        }

        // --------------------------
        // Blog routes (optional)
        // e.g. blog list / blog/post/{slug}
        // --------------------------
        if (request()->routeIs('blog.details') && isset($params['slug'])) {
            // show blog post title
            // implement as needed
            $crumbs[] = [
                'label' => 'Blog',
                'url' => route('blog.list'),
            ];
            // then append post title if you want (requires Post model)
            return $crumbs;
        }

        if (request()->routeIs('blog.list') && isset($params['categories'])) {
            // similar to product categories if you have blog categories
            return $crumbs;
        }

        // --------------------------
        // Search page
        // --------------------------
        if (request()->routeIs('search')) {
            $crumbs[] = [
                'label' => 'Search',
                'url' => url()->current(),
            ];
            return $crumbs;
        }

        // fallback: return home only (no breadcrumb section)
        return $crumbs;
    }
}
