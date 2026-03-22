<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Page;
use App\Models\BlogPost;
use App\Models\BlogCategory;

class BreadcrumbService
{
    /**
     * Generate breadcrumbs for the current request.
     * Each item: ['label' => string, 'url' => string]
     */
    public static function generate(): array
    {
        $crumbs = self::base();
        $params = request()->route()?->parameters() ?? [];
        if (request()->route() === null) {
            return self::for404($crumbs);
        }

        return match (true) {
            request()->routeIs('products.list*') => self::forProductCategory($crumbs, $params),
            request()->routeIs('products.details') => self::forProductDetails($crumbs, $params),
            request()->routeIs('blog.details') => self::forBlogDetails($crumbs, $params),
            request()->routeIs('blog.list') => self::forBlogList($crumbs, $params),
            request()->routeIs('search') => self::forSearch($crumbs),
            request()->routeIs('page') => self::forPage($crumbs, $params),
            request()->routeIs('profile*') => self::forProfile($crumbs),
            request()->routeIs('login') => self::forLogin($crumbs),
            request()->routeIs('register') => self::forRegister($crumbs),
            request()->routeIs('password.request') => self::forForgotPassword($crumbs),
            request()->routeIs('password.reset*') => self::forResetPassword($crumbs),
            request()->routeIs('errors.404') => self::for404($crumbs),
            default => $crumbs,
        };
    }

    private static function base(): array
    {
        return [
            ['label' => 'Home', 'url' => url('/')],
        ];
    }

    private static function append(array $crumbs, string $label, string $url): array
    {
        $crumbs[] = ['label' => $label, 'url' => $url];
        return $crumbs;
    }

    private static function forProductCategory(array $crumbs, array $params): array
    {
        if (empty($params['categories'])) {
            return $crumbs;
        }

        $slug = last(explode('/', trim($params['categories'], '/')));
        $category = ProductCategory::where('slug', $slug)->first();

        if ($category) {
            foreach ($category->getBreadcrumbs() as $crumb) {
                $crumbs[] = $crumb;
            }
        }

        return $crumbs;
    }

    private static function forProductDetails(array $crumbs, array $params): array
    {
        if (empty($params['slug'])) {
            return $crumbs;
        }

        $product = Product::with('categories')->where('slug', $params['slug'])->first();

        if (!$product) {
            return $crumbs;
        }

        if ($product->categories->isNotEmpty()) {
            foreach ($product->categories->first()->getBreadcrumbs() as $crumb) {
                $crumbs[] = $crumb;
            }
        }

        return self::append(
            $crumbs,
            $product->title ?? $product->name ?? 'Product',
            route('products.details', $product->slug)
        );
    }

    private static function forBlogList(array $crumbs, array $params): array
    {
        $crumbs = self::append($crumbs, 'Blog', route('blog.list'));

        if (empty($params['categories'])) {
            return $crumbs;
        }

        $slug = last(explode('/', trim($params['categories'], '/')));
        $category = BlogCategory::where('slug', $slug)->first();

        if ($category) {
            if (method_exists($category, 'getBreadcrumbs')) {
                foreach ($category->getBreadcrumbs() as $crumb) {
                    $crumbs[] = $crumb;
                }
            } else {
                $crumbs = self::append($crumbs, $category->name, route('blog.list', $category->slug));
            }
        }

        return $crumbs;
    }

    private static function forBlogDetails(array $crumbs, array $params): array
    {
        $crumbs = self::append($crumbs, 'Blog', route('blog.list'));

        if (empty($params['slug'])) {
            return $crumbs;
        }

        $post = BlogPost::where('slug', $params['slug'])->first();

        if (!$post) {
            return $crumbs;
        }

        if (!empty($post->category)) {
            $crumbs = self::append(
                $crumbs,
                $post->category->name,
                route('blog.list', $post->category->slug)
            );
        }

        return self::append(
            $crumbs,
            $post->title,
            route('blog.details', $post->slug)
        );
    }

    private static function forPage(array $crumbs, array $params): array
    {
        if (empty($params['slug'])) {
            return $crumbs;
        }
        $page = Page::where('slug', $params['slug'])->first();
        if (!$page) {
            return self::for404($crumbs);
        }

        return self::append($crumbs, $page->title, url()->current());
    }

    private static function forProfile(array $crumbs): array
    {
        return self::append($crumbs, 'My Account', route('profile.dashboard', absolute: false));
    }

    private static function forSearch(array $crumbs): array
    {
        $query = request()->input('q');

        $label = $query
            ? 'Search: ' . $query
            : 'Search';

        return self::append($crumbs, $label, url()->current());
    }
    private static function forLogin(array $crumbs): array
    {
        return self::append($crumbs, 'Login', route('login'));
    }

    private static function forRegister(array $crumbs): array
    {
        return self::append($crumbs, 'Register', route('register'));
    }

    private static function forForgotPassword(array $crumbs): array
    {
        return self::append($crumbs, 'Forgot Password', route('password.request'));
    }
    private static function forResetPassword(array $crumbs): array
    {
        // Fixed: correct label and route — token is required by Laravel's password.reset route
        return self::append(
            $crumbs,
            'Reset Password',
            route('password.reset', ['token' => request()->route('token')])
        );
    }
    private static function for404(array $crumbs): array
    {
        return self::append($crumbs, 'Page Not Found', url()->current());
    }
}
