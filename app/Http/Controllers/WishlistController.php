<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with('wishlistable')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        // Used only for UI (heart color)
        $wishlistedProductIds = $wishlists->pluck('wishlistable_id')->toArray();

        return view('frontend.wishlist', compact(
            'wishlists',
            'wishlistedProductIds'
        ));
    }



    public function toggle(Product $product): JsonResponse
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'You must be logged in to manage the wishlist.'
                ], 401);
            }

            $wishlist = Wishlist::where([
                'user_id' => $userId,
                'wishlistable_id' => $product->id,
                'wishlistable_type' => Product::class,
            ])->first();

            if ($wishlist) {
                $wishlist->delete();
                return response()->json([
                    'status' => true,
                    'action' => 'removed',
                    'message' => 'Product removed from wishlist.'
                ]);
            }

            Wishlist::create([
                'user_id' => $userId,
                'wishlistable_id' => $product->id,
                'wishlistable_type' => Product::class,
            ]);

            return response()->json([
                'status' => true,
                'action' => 'added',
                'message' => 'Product added to wishlist.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Could not update wishlist. Please try again.'
            ], 500);
        }
    }


    public function count()
    {
        try {
            $userId = Auth::id();

            $count = $userId ? Wishlist::where('user_id', $userId)->count() : 0;

            return response()->json([
                'status' => true,
                'count' => $count,
                'message' => 'Wishlist count retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'count' => 0,
                'error' => $e->getMessage(),
                'message' => config('app.debug') ? $e->getMessage() : 'Could not retrieve wishlist count. Please try again.'
            ], 500);
        }
    }
}
