<?php

use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;


if (!function_exists('isActiveRoute')) {
    /**
     * Check if the current route matches the given route(s)
     * and return a CSS class (like "active").
     */
    function isActiveRoute($routeNames, $class = 'active')
    {
        if (is_array($routeNames)) {
            foreach ($routeNames as $route) {
                if (request()->routeIs($route)) {
                    return $class;
                }
            }
            return '';
        }

        return request()->routeIs($routeNames) ? $class : '';
    }
}

if (!function_exists('dateFormat')) {
    /**
     * Format a date using Carbon
     */
    function dateFormat($date)
    {
        if (!$date)
            return null;

        // If already Carbon, use it directly
        $dt = $date instanceof \Carbon\Carbon
            ? $date
            : \Carbon\Carbon::parse($date);

        // Convert to app timezone BEFORE formatting
        $dt = $dt->timezone(config('app.timezone'));

        // Show time only if it's not midnight
        if ($dt->format('H:i:s') !== '00:00:00') {
            return $dt->format('d M Y H:i');
        }

        return $dt->format('d M Y');
    }

}

if (!function_exists('flashMessage')) {
    /**
     * Set a flash message to display in Blade
     */
    function flashMessage($message, $type = 'success')
    {
        Session::flash('message', $message);
        Session::flash('message_type', $type);
    }
}

if (!function_exists('currencyformat')) {
    /**
     * Format a number as currency using saved settings.
     *
     * @param float|int $amount
     * @param bool $with_symbol Whether to include the currency symbol
     * @return string
     */
    function currencyformat($amount, $with_symbol = true)
    {
        // Get symbol from settings (fallback to ₹)
        $symbol = setting('currency_symbol', '₹');

        $formatted = number_format($amount, 2, '.', ',');

        return $with_symbol ? $symbol . $formatted : $formatted;
    }
    //     {{ currencyformat(1500) }}  
// {{-- Output: ₹1,500.00 (if your saved currency symbol is ₹) --}}
// {{ currencyformat(1500, false) }}
// {{-- Output: 1,500.00 --}}

}


if (!function_exists('status_badge')) {
    function status_badge($status): HtmlString
    {
        $statuses = [
            1 => ['Active', 'success'],
            0 => ['Inactive', 'danger'],
            2 => ['Suspended', 'warning'],
        ];

        [$label, $color] = $statuses[$status] ?? ['Unknown', 'secondary'];

        return new HtmlString("<span class='badge bg-{$color}'>{$label}</span>");
    }
}


if (!function_exists('paymentStatusBadge')) {

    function paymentStatusBadge($status)
    {
        return match ($status) {
            'pending' => [
                'class' => 'bg-warning text-dark',
                'icon'  => 'fa-hourglass-half',
                'text'  => 'Pending',
            ],
            'completed' => [
                'class' => 'bg-success',
                'icon'  => 'fa-check',
                'text'  => 'Completed',
            ],
            'cancelled' => [
                'class' => 'bg-danger',
                'icon'  => 'fa-times',
                'text'  => 'Cancelled',
            ],
            default => [
                'class' => 'bg-secondary',
                'icon'  => 'fa-circle',
                'text'  => ucfirst($status),
            ],
        };
    }
}

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return Setting::get($key, $default);
    }
    //  {{ setting('currency_symbol') }}
}

if (!function_exists('enabledPaymentGateways')) {
    function enabledPaymentGateways()
    {
        return collect(json_decode(setting('payment_gateways') ?? '{}', true))
            ->filter(fn($g) => isset($g['enabled']) && (int) $g['enabled'] === 1);
    }
}
if (!function_exists('enabledShippingMethods')) {
    // function enabledShippingMethods()
    // {
    //     return collect(json_decode(setting('shipping_methods') ?? '{}', true))
    //         ->filter(fn($g) => isset($g['enabled']) && (int) $g['enabled'] === 1);
    // }
    function enabledShippingMethods()
    {
        return collect(json_decode(setting('shipping_methods') ?? '{}', true))
            ->filter(fn($g) => isset($g['enabled']) && (int) $g['enabled'] === 1)
            ->reverse();
            //->values(); // optional: reindex keys
    }
}
if (!function_exists('labelFromKey')) {
    function labelFromKey(string $value): string
    {
        return ucwords(str_replace('_', ' ', $value));
    }
}

if (!function_exists('image_url')) {
    /**
     * Get public URL of an image by type and size.
     *
     * Example:
     *   image_url('article', 'uuid_medium.webp', 'medium')
     *
     * @param string $type       e.g. 'article', 'product'
     * @param string|null $filename
     * @param string $size       e.g. 'icon', 'small', 'medium', 'large', 'original'
     * @return string|null
     */
    function image_url(string $type, ?string $filename, string $size = 'original'): ?string
    {
        if (!$filename) {
            return null;
        }

        $config = config("images.$type");

        if (!$config) {
            throw new \Exception("Image type '$type' not found in config/images.php");
        }

        $folder = $config['path'];

        // Optional subfolder per size (if you saved like /uploads/articles/small/)
        $sizeFolder = in_array($size, ['icon', 'small', 'medium', 'large', 'original'])
            ? $folder . '/' . $size
            : $folder;

        return asset('storage/' . trim($sizeFolder, '/') . '/' . $filename);
    }
}

if (!function_exists('cart')) {
    function cart()
    {
        return app(\App\Services\CartService::class);
    }
}

if (!function_exists('wishlist')) {
    function wishlist()
    {
        return app(\App\Services\WishlistService::class);
    }
}



