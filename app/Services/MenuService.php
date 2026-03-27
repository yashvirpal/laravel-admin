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
     

        return Cache::remember("header_menu", 3600, function ()  {
            $categories = ProductCategory::query()
                ->select('id', 'name', 'slug', 'parent_id', 'menuOrder')
                ->active()
               
                ->whereNull('parent_id')
                ->with([
                    'children' => function ($q) {
                        $q->select('id', 'name', 'slug', 'parent_id')
                            ->active();
                    }
                ])
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
       

        return Cache::remember("footer_guide_menu", 3600, function ()  {
            // Fetch parent slug once, outside the loop
            $parentSlug = Cache::remember(
                'category_slug_29',
                86400,
                fn() => ProductCategory::where('id', 29)->value('slug') ?? ''
            );

            $categories = ProductCategory::query()
                ->select('id', 'name', 'slug', 'parent_id')
                ->active()
              
                ->where('parent_id', 29)
                ->get()
                ->each(fn($child) => $child->setAttribute('full_slug', $parentSlug . '/' . $child->slug));

            return [
                'categories' => $categories,
                'pageMenuData' => "",//(12),
            ];
        });
    }

    public function QuickLinks()
    {
       

        return Cache::remember("footer_quick_links_menu", 3600, function () {
            $pages = Page::active()
                ->select('id', 'title', 'slug')
                ->whereIn('id', [ 2, 3, 5, 6, 9, 10])
                ->orderBy('id', 'asc')
                ->get();

            return ['pages' => $pages];
        });
    }
}