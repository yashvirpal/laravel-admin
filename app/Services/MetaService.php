<?php

namespace App\Services;


class MetaService
{
    public function __construct(
        protected MenuService $menuService
    ) {
    }

    public static function generate($model = null, $overrides = [], $listitems = null)
    {
        $searchTerm = request()->get('q') ?? null;
        $isSearch = $searchTerm !== null;
        $is404 = $overrides['is404'] ?? false;

        $title = self::makeTitle($model, $overrides, $isSearch, $searchTerm);
        $description = self::makeDescription($model, $overrides, $isSearch, $searchTerm);
        $keywords = self::makeKeywords($model, $overrides, $isSearch, $searchTerm);

        // $image = $overrides['image'] ?? ($model->seo_image_url ?? asset('assets/images2/logo.webp'));
        $image = collect([
            $overrides['image'] ?? null,
            $model->seo_image_url ?? null,
            $model->image_url ?? null,
            asset('assets/images2/logo.webp'),
        ])->first(fn($img) => filled($img));
        $canonical = self::canonicalUrl();
        $hreflang = self::hreflangUrls();
        $schema = self::generateSchema($model, $isSearch, $is404, $listitems);
        $ogType = self::getOgType($model, $isSearch);


        return compact(
            'title',
            'description',
            'keywords',
            'image',
            'canonical',
            'hreflang',
            'schema',
            'isSearch',
            'is404',
            'ogType'
        );
    }


    /** TITLE */
    protected static function makeTitle($model, $overrides, $isSearch, $searchTerm)
    {
        if ($isSearch) {
            return "Search results for \"$searchTerm\" | " . config('app.name');
        }

        return $overrides['title'] ?? ($model->meta_title ?? $model->title ?? $model->name ?? config('app.name'));
    }


    /** DESCRIPTION */
    protected static function makeDescription($model, $overrides, $isSearch, $searchTerm)
    {
        if ($isSearch) {
            return "Search results for \"$searchTerm\" on " . config('app.name');
        }

        return $overrides['description'] ?? ($model->meta_description ?? "Latest crypto news and insights.");
    }


    /** KEYWORDS */
    protected static function makeKeywords($model, $overrides, $isSearch, $searchTerm)
    {
        if ($isSearch) {
            return "search, {$searchTerm}, crypto";
        }

        return $overrides['keywords'] ?? ($model->meta_keywords ?? "crypto, bitcoin, blockchain, trading");
    }

    /** OG Type */
    protected static function getOgType($model, $isSearch)
    {
        if ($model && method_exists($model, 'getTable') && $model->getTable() === 'articles') {
            return 'article';
        }
        return 'website';
    }

    /** CANONICAL URL with pagination support */
    protected static function canonicalUrl()
    {
        if (request()->page && request()->page > 1) {
            return url()->current() . '?page=' . request()->page;
        }
        return url()->current();
    }


    /** HREFLANG (Optional: Add your locales here) */
    protected static function hreflangUrls()
    {
        $locales = ['en', 'hi']; // Example: English + Hindi

        $urls = [];
        foreach ($locales as $locale) {
            $urls[$locale] = url()->current() . '?lang=' . $locale;
        }

        return $urls;
    }

    /** MAIN SCHEMA BUILDER */
    protected static function generateSchema($model, $isSearch, $is404 = false, $listItems = null)
    {

        $schema = [];

        // Organization Schema (universal)
        $schema[] = self::organizationSchema();

        // Website Schema (homepage)
        //if (request()->routeIs('home')) {
        $schema[] = self::websiteSchema();
        // }

        // Search Schema
        if ($isSearch) {
            $schema[] = self::searchSchema();
            //return $schema;
        }

        // 404 Schema
        if ($is404) {
            $schema[] = self::notFoundSchema();
            //return $schema;
        }


        // 404 Schema
        // if (view()->exists('errors.404') && response()->getStatusCode() === 404) {
        //     return [
        //         self::organizationSchema(),
        //         self::notFoundSchema()
        //     ];
        // }

        // // Article Schema
        // if ($model && method_exists($model, 'getTable') && $model->getTable() === 'articles') {
        //     $schema[] = self::articleSchema($model);
        //     $faqSchema = self::faqSchema($model);
        //     if ($faqSchema) {
        //         $schema[] = $faqSchema;
        //     }
        // }

        // // Category page
        // if ($model && method_exists($model, 'getTable') && $model->getTable() === 'categories') {
        //     $schema[] = self::categorySchema($model, $listItems);
        // }

        // // Author page
        // if ($model && method_exists($model, 'getTable') && $model->getTable() === 'authors') {
        //     // $schema[] = self::authorSchema($model);
        //     //  $schema[] = self::profilePageSchema($model);
        //     $schema[] = self::authorProfileSchema($model, $listItems);
        // }
        // if (isset($model->template) && $model->template === "author-list") {
        //     $schema[] = self::authorListSchema($model, $listItems);
        // }

        if (!request()->routeIs('home')) {

            // Search page breadcrumb
            if (request()->routeIs('search')) {
                $schema[] = self::breadcrumbSchema();
            }

            // Article / Category breadcrumb
            elseif ($model) {
                $schema[] = self::breadcrumbSchema($model);
            }
        }
        // $headerMenu = app(MenuService::class)->header();

        // $schema[] = self::navigationSchemaFromMenu($headerMenu);
        return $schema;
    }


    /** ORGANIZATION SCHEMA */
    protected static function organizationSchema()
    {
        return [
            "@context" => "https://schema.org",
            "@type" => ["Organization", "NewsMediaOrganization"],//"Organization",//NewsMediaOrganization
            "name" => config('app.name'),
            "description" => "Crypy is a digital media and research platform focused on blockchain technology, cryptocurrencies, and the digital asset ecosystem. It provides news, analysis, educational content, and industry insights for informational purposes only.",
            "url" => url('/'),
            "logo" => [
                "@type" => "ImageObject",
                "url" => asset('assets/images2/logo.webp'),
                "width" => 150,
                "height" => 52
            ],

            // "logo" => asset('assets/images2/logo.webp'),
            "email" => "info@crypy.ai",
            "telephone" => "+91 9958435100",
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => "A4 and A5, Logix Business Park, A Block, Sector 16",
                "addressLocality" => "Noida",
                "addressRegion" => "Uttar Pradesh",
                "addressCountry" => "IN",
                "postalCode" => "201301",
            ],

            // "foundingDate" => "2026-01-01",
            // "publisher" => [
            //     "@type" => "Organization",
            //     "name" => "Kovus Fintech Solutions Pvt Ltd"
            // ],
            "contactPoint" => [
                "@type" => "ContactPoint",
                "contactType" => "customer support",
                "email" => "support@crypy.ai",
                "availableLanguage" => ["English"]
            ],
            "parentOrganization" => [
                "@type" => "Organization",
                "name" => "Kovus Fintech Solutions Pvt Ltd"
            ],
            "sameAs" => [
                "https://x.com/CrypyAi",
                'https://www.facebook.com/profile.php?id=61587663398523',
                'https://www.linkedin.com/in/crypy-ai-6967943ab/',
                "https://www.instagram.com/crypyai/"
            ]
            // "vatID" => "FR12345678901",
            // "iso6523Code" => "0199:724500PMK2A2M1SQQ228",
        ];

    }


    /** WEBSITE SCHEMA */
    protected static function websiteSchema()
    {
        $headerMenu = app(MenuService::class)->header();
        return [
            "@context" => "https://schema.org",
            "@type" => "WebSite",
            "url" => url('/'),
            "publisher" => [
                "@type" => "Organization",
                "name" => config('app.name'),
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => asset('assets/images2/logo.webp'),
                    "width" => 150,
                    "height" => 52
                ],
            ],
            "name" => config('app.name'),
            //  "description" => "Crypy is a digital media and research platform focused on blockchain technology, cryptocurrencies, and the digital asset ecosystem. It provides news, analysis, educational content, and industry insights for informational purposes only.",
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => url('/search?q={search_term_string}'),
                "query-input" => "required name=search_term_string",
            ],
            'hasPart' => self::navigationSchemaFromMenu($headerMenu),
        ];
    }


    /** SEARCH SCHEMA */
    protected static function searchSchema(): array
    {
        return [
            "@context" => "https://schema.org",
            "@type" => "SearchResultsPage",
            "name" => 'Search results for "' . request('q') . '"',
            "url" => url()->current() . '?q=' . request('q'),
            "isPartOf" => [
                "@type" => "WebSite",
                "name" => "Crypy",
                "url" => url('/'),
            ],
        ];
    }


    /** 404 SCHEMA */
    protected static function notFoundSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'Page Not Found',
            'description' => 'The page you are looking for does not exist.',
            'url' => url()->current(),
            'mainEntity' => [
                '@type' => 'Thing',
                'name' => '404 Error',
            ],
        ];
    }

    /** CATEGORY SCHEMA */

    protected static function categorySchema($category, $articles)
    {
        $categoryUrl = route('categoryArticle', $category->full_slug);
        $websiteUrl = url('/');

        $itemListElements = [];
        $graphArticles = [];

        foreach ($articles->values() as $index => $article) {

            $articleUrl = route('categoryArticle', $article->slug);

            $type = match ($article->schema_type) {
                'newsPost' => 'NewsArticle',
                'blogPost' => 'BlogPosting',
                default => 'Article',
            };

            $graphArticles[] = [
                '@type' => $type,
                '@id' => $articleUrl . '#article',
                'headline' => $article->title,
                'url' => $articleUrl,
                'datePublished' => $article->published_at?->toIso8601String(),
                'author' => $article->author
                    ? ['@id' => route('author', $article->author->slug) . '#person']
                    : null,
                'publisher' => [
                    '@id' => $websiteUrl . '#organization'
                ],
            ];

            $itemListElements[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@id' => $articleUrl . '#article'
                ],
            ];
        }
        $graphArticles = [];

        return [
            '@context' => 'https://schema.org',
            '@graph' => array_merge([

                [
                    '@type' => 'CollectionPage',
                    '@id' => $categoryUrl . '#collectionpage',
                    'url' => $categoryUrl,
                    'name' => $category->meta_title ?? $category->name,
                    'description' => $category->meta_description
                        ?? strip_tags($category->description),
                    'isPartOf' => [
                        '@id' => $websiteUrl . '#website',
                    ],
                    'mainEntity' => [
                        '@id' => $categoryUrl . '#itemlist'
                    ],
                    'pagination' => [
                        'next' => $articles->nextPageUrl(),
                        'prev' => $articles->previousPageUrl(),
                    ],
                ],

                [
                    '@type' => 'ItemList',
                    '@id' => $categoryUrl . '#itemlist',
                    'name' => $category->name,
                    'itemListOrder' => 'https://schema.org/ItemListOrderDescending',
                    'numberOfItems' => $articles->total(),
                    'itemListElement' => $itemListElements,
                ],

            ], $graphArticles),
        ];
    }



    /* AUTHOR LIST SCHEMA */
    protected static function authorListSchema($model, $authors)
    {

        $itemList = [];

        foreach ($authors->values() as $index => $author) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => route('author', $author->slug),
                'item' => [
                    '@type' => 'Person',
                    'name' => $author->name,
                    'url' => route('author', $author->slug),
                    'image' => $author->profile_image
                        ? image_url('author', $author->profile_image, 'medium')
                        : null,
                    'jobTitle' => $author->designation ?? 'Content Writer',
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            '@id' => route('page', $model->slug) . '#collection',
            'name' => $model->meta_title ?? 'Authors - ' . config('app.name'),
            'description' => $model->meta_description ?? 'List of authors at ' . config('app.name'),
            'url' => route('page', $model->slug),

            'isPartOf' => [
                '@type' => 'WebSite',
                '@id' => url('/') . '#website',
            ],

            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $authors->count(),
                'itemListElement' => $itemList,
            ],
        ];
    }


    /** AUTHOR SCHEMA */
    protected static function authorSchema($author)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Person',

            'name' => $author->name,

            'url' => route('author', $author->slug),

            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('author', $author->slug),
            ],

            'description' => strip_tags($author->bio),

            'image' => [
                '@type' => 'ImageObject',
                'url' => $author->profile_image_url
                    ?? asset('frontend/images/author-default.jpg'),
            ],

            'jobTitle' => $author->designation ?? 'Content Writer',

            'worksFor' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'url' => url('/'),
            ],

            'sameAs' => array_values(array_filter([
                $author->twitter_handle ? 'https://twitter.com/' . ltrim($author->twitter_handle, '@') : null,

                $author->linkedin_handle ? 'https://www.linkedin.com/in/' . $author->linkedin_handle : null,

                $author->facebook_handle ? 'https://www.facebook.com/' . $author->facebook_handle : null,

                $author->instagram_handle ? 'https://www.instagram.com/' . $author->instagram_handle : null,

                $author->snapchat_handle ? 'https://www.instagram.com/' . $author->snapchat_handle : null,
            ])),
        ];
    }

    /* ProfilePage schema */
    protected static function profilePageSchema($author)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',

            'mainEntity' => [
                '@type' => 'Person',

                'name' => $author->name,

                'url' => route('author', $author->slug),

                'description' => strip_tags($author->bio),

                'image' => [
                    '@type' => 'ImageObject',
                    'url' => $author->profile_image_url
                        ?? asset('frontend/images/author-default.jpg'),
                ],

                'jobTitle' => $author->designation ?? 'Content Writer',

                'worksFor' => [
                    '@type' => 'Organization',
                    'name' => config('app.name'),
                    'url' => url('/'),
                ],

                'sameAs' => array_values(array_filter([
                    $author->twitter_handle ? 'https://twitter.com/' . ltrim($author->twitter_handle, '@') : null,
                    $author->linkedin_handle ? 'https://www.linkedin.com/in/' . $author->linkedin_handle : null,
                    $author->facebook_handle ? 'https://www.facebook.com/' . $author->facebook_handle : null,
                    $author->instagram_handle ? 'https://www.instagram.com/' . $author->instagram_handle : null,
                    $author->snapchat_handle ? 'https://www.snapchat.com/add/' . $author->snapchat_handle : null,
                ])),
            ],

            'url' => route('author', $author->slug),
        ];
    }

    protected static function authorProfileSchema($author, $articles = null)
    {
        //dd($articles);
        $authorUrl = route('author', $author->slug);
        $websiteUrl = url('/');

        $personId = $authorUrl . '#person';
        $profilePageId = $authorUrl . '#profilepage';
        $itemListId = $authorUrl . '#latest-articles';

        return [
            '@context' => 'https://schema.org',
            '@graph' => [

                [
                    '@type' => 'ProfilePage',
                    '@id' => $profilePageId,
                    'url' => $authorUrl,
                    'name' => $author->name . ' - Author Profile',
                    'mainEntity' => ['@id' => $personId],
                    'isPartOf' => [
                        '@type' => 'WebSite',
                        '@id' => $websiteUrl . '#website',
                    ],
                    'pagination' => [
                        'next' => $articles->nextPageUrl(),
                        'prev' => $articles->previousPageUrl(),
                    ],
                ],

                [
                    '@type' => 'Person',
                    '@id' => $personId,
                    'name' => $author->name,
                    'url' => $authorUrl,
                    'description' => strip_tags($author->bio),
                    'image' => [
                        '@type' => 'ImageObject',
                        'url' => $author->profile_image_url
                            ?? asset('frontend/images/author-default.jpg'),
                    ],
                    'jobTitle' => $author->designation ?? 'Content Writer',
                    'worksFor' => [
                        '@type' => 'Organization',
                        '@id' => $websiteUrl . '#organization',
                    ],
                    'sameAs' => array_values(array_filter([
                        $author->twitter_handle ? 'https://twitter.com/' . ltrim($author->twitter_handle, '@') : null,
                        $author->linkedin_handle ? 'https://www.linkedin.com/in/' . $author->linkedin_handle : null,
                        $author->facebook_handle ? 'https://www.facebook.com/' . $author->facebook_handle : null,
                        $author->instagram_handle ? 'https://www.instagram.com/' . $author->instagram_handle : null,
                        $author->snapchat_handle ? 'https://www.snapchat.com/add/' . $author->snapchat_handle : null,
                    ])),
                ],

                [
                    '@type' => 'ItemList',
                    '@id' => $itemListId,
                    'name' => 'Latest articles from ' . $author->name,
                    'itemListOrder' => 'https://schema.org/ItemListOrderDescending',
                    'numberOfItems' => $articles->total(),
                    'itemListElement' => $articles->values()->map(function ($article, $index) use ($personId, $websiteUrl) {

                        $articleUrl = route('categoryArticle', $article->slug);

                        return [
                            '@type' => 'ListItem',
                            'position' => $index + 1,
                            'item' => [
                                '@type' => 'NewsArticle',
                                '@id' => $articleUrl . '#article',
                                'headline' => $article->title,
                                'url' => $articleUrl,
                                'datePublished' => $article->published_at->timezone('Asia/Kolkata')->format('c'),
                                'author' => ['@id' => $personId],
                                'publisher' => ['@id' => $websiteUrl . '#organization'],
                            ],
                        ];
                    })->toArray(),
                ],

            ],
        ];
    }


    /** ARTICLE SCHEMA */
    protected static function articleSchema($article)
    {
        if (!$article) {
            return [];
        }
        // Default type
        $type = 'Article';

        if ($article->schema_type === 'news') {
            $type = 'NewsArticle';
        } elseif ($article->schema_type === 'blogPost') {
            $type = 'BlogPosting';
        }


        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'headline' => $article->meta_title ?? $article->title,
            'description' => $article->meta_description ?? config('app.name') . ', crypto, news',
            // 'image' => $article->seo_image_url ?? $article->image_url,
            // 'mainEntityOfPage' => url()->current(),
            "image" => [
                "@type" => "ImageObject",
                "url" => $article->seo_image_url ?? $article->image_url ?? asset('assets/images2/logo.webp'),
            ],
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id" => url()->current()
            ],
            'author' => [
                '@type' => 'Person',
                'name' => optional($article->author)->name,
                'url' => optional($article->author)
                    ? route('author', $article->author->slug)
                    : null,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('frontend/images2/logo.webp'),
                ],
            ],
            'datePublished' => $article->published_at->timezone('Asia/Kolkata')->format('c'),
            'dateModified' => $article->updated_at->timezone('Asia/Kolkata')->format('c'),
            'keywords' => $article->meta_keywords ?? config('app.name') . ', crypto, news',
            'articleBody' => strip_tags($article->description),
            // Optional: Add more properties as needed
        ];

        if ($article->type == 1 && !empty($article->video_link)) {

            // Convert YouTube link to embed format


            $videoId = youtube_id_from_url($article->video_link);

            if ($videoId) {
                $embedUrl = "https://www.youtube.com/embed/{$videoId}";
            } else {
                $embedUrl = $article->video_link;
            }

            $schema['video'] = [

                '@type' => 'VideoObject',
                'name' => $article->title,
                'description' => $article->meta_description ?? $article->title,
                'thumbnailUrl' => $article->seo_image_url ?? $article->image_url ?? asset('assets/images2/logo.webp'),
                // "thumbnailUrl" => "https://img.youtube.com/vi/sxRstg8ZJPk/maxresdefault.jpg",
                // "contentUrl" => "https://www.youtube.com/watch?v=sxRstg8ZJPk",
                // "uploadDate" => "2025-12-17T11:37:00+05:30",
                'uploadDate' => $article->published_at->timezone('Asia/Kolkata')->format('c'),
                // For YouTube use embedUrl
                'embedUrl' => $embedUrl,
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => config('app.name'),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => asset('frontend/images2/logo.webp'),
                    ],
                ],
            ];
        }


        return $schema;
    }




    /** BREADCRUMB SCHEMA */
    protected static function breadcrumbSchema(): array
    {
        return $items = [];
        $crumbs = breadcrumbs();
        $items = [];

        foreach ($crumbs as $index => $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /** NAVIGATION SCHEMA */
    /**
     * Generate structured JSON-LD for navigation menu with positions and nested children
     */
    protected static function navigationSchemaFromMenu(array $headerMenu): array
    {
        $position = 1;
     return   $items = [];

        // Home
        if (!empty($headerMenu['home'])) {
            $items[] = [
                '@type' => 'SiteNavigationElement',
                'position' => $position++,
                'name' => $headerMenu['home']['title'] ?? 'Home',
                'url' => $headerMenu['home']['url'] ?? url('/'),
            ];
        }

        // Categories (with children as hasPart)
        if (!empty($headerMenu['categories'])) {
            foreach ($headerMenu['categories'] as $category) {

                $categoryItem = [
                    '@type' => 'SiteNavigationElement',
                    'position' => $position++,
                    'name' => $category->name,
                    'url' => route('categoryArticle', $category->full_slug),
                ];

                // If category has children
                if ($category->children && $category->children->count()) {

                    $childPosition = 1;
                    $categoryItem['hasPart'] = [];

                    foreach ($category->children as $child) {
                        $categoryItem['hasPart'][] = [
                            '@type' => 'SiteNavigationElement',
                            'position' => $childPosition++,
                            'name' => $child->name,
                            'url' => route('categoryArticle', $child->full_slug),
                        ];
                    }
                }

                $items[] = $categoryItem;
            }
        }

        // Contact
        if (!empty($headerMenu['HeaderPageMenuData'])) {
            $items[] = [
                '@type' => 'SiteNavigationElement',
                'position' => $position++,
                'name' => $headerMenu['HeaderPageMenuData']->title ?? 'Page menu title Not Found',
                'url' => route('page', $headerMenu['HeaderPageMenuData']->slug) ?? "",
            ];
        }

        return $items;
    }



    /**
     * Generate FAQ schema for an article
     *
     * @param \App\Models\Article $article
     * @return array|null
     */
    protected static function faqSchema($article): ?array
    {
        if (!$article->faqs || $article->faqs->isEmpty()) {
            return null;
        }

        $mainEntity = [];

        foreach ($article->faqs as $faq) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => strip_tags($faq->question),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($faq->answer),
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }

}
