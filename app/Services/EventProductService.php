<?php

namespace App\Services;

use App\Models\EventProduct;

class EventProductService
{
    public function create(array $data): EventProduct
    {


        return EventProduct::create($data);
    }

    public function get()
    {
        $eventProducts = EventProduct::with(['restaurantProduct.restaurant.restaurantBranches.city'])->where('is_active', '=', 1)->get();
        return $eventProducts;
    }

    public function filter($idEvent = 0, $idProduct = 0)
    {
        $eventProducts = EventProduct::with(['restaurantProduct.restaurant.restaurantBranches.city'])
        ->where('is_active', '=', 1)
        ->where('event_id', '=', $idEvent)
        ->where('product_id', '=', $idProduct)->get();
        return $eventProducts;
    }
}
