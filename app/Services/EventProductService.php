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
        $eventProducts = EventProduct::with(['restaurantProduct'])->where('is_active','=',1)->get();
        return $eventProducts;
    }
}
