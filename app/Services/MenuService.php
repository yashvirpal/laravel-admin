<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ProductCategory;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    public function header()
    {
        $domain = app()->bound('currentDomain') ? app('currentDomain') : null;

        return Cache::remember("header_menu_{$domain?->id}", 3600, function () use ($domain) {
            $categories = ProductCategory::query()
                ->select('id', 'name', 'slug', 'parent_id', 'menuOrder')
                ->active()
                ->when($domain, fn($q) => $q->forDomain($domain->id))
                ->whereNull('parent_id')
                ->with([
                    'children' => function ($q) {
                        $q->select('id', 'name', 'slug', 'parent_id', 'menuOrder')
                            ->active()
                            ->orderBy('menuOrder', 'asc')
                            ->where('menuOrder', '>', 0);
                    }
                ])
                ->orderBy('menuOrder', 'asc')
                ->where('menuOrder', '>', 0)
                ->get()
                ->each(function ($category) {
                    $category->setAttribute('full_slug', $category->slug);
                    $category->children->each(function ($child) use ($category) {
                        $child->setAttribute('full_slug', $category->slug . '/' . $child->slug);
                    });
                });

            return [
                'home' => ['title' => 'Home', 'url' => url('/')],
                'categories' => $categories,
                'HeaderPageMenuData' => '',
                'contact' => ['title' => 'Contact Us', 'url' => url('/page/contact-us')],
            ];
        });
    }

    public function FooterGuide()
    {
        $domain = app()->bound('currentDomain') ? app('currentDomain') : null;

        return Cache::remember("footer_guide_menu_{$domain?->id}", 3600, function () use ($domain) {
            // Fetch parent slug once, outside the loop
            $parentSlug = Cache::remember(
                'category_slug_29',
                86400,
                fn() => Category::where('id', 29)->value('slug') ?? ''
            );

            $categories = Category::query()
                ->select('id', 'name', 'slug', 'parent_id')
                ->active()
                ->when($domain?->id, fn($q) => $q->forDomain($domain->id))
                ->where('parent_id', 29)
                ->orderBy('menuOrder', 'asc')
                ->get()
                ->each(fn($child) => $child->setAttribute('full_slug', $parentSlug . '/' . $child->slug));

            return [
                'categories' => $categories,
                'pageMenuData' => getPageData(12),
            ];
        });
    }

    public function QuickLinks()
    {
        $domain = app()->bound('currentDomain') ? app('currentDomain') : null;

        return Cache::remember("footer_quick_links_menu_{$domain?->id}", 3600, function () {
            $pages = Page::active()
                ->select('id', 'title', 'slug')
                ->whereIn('id', [ 2, 3, 5, 6, 9, 10])
                ->orderBy('id', 'asc')
                ->get();

            return ['pages' => $pages];
        });
    }
}