<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class WishlistServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wishlist';
    }
}
