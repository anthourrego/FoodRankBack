<?php

namespace App\Services;
use App\Models\RestaurantProduct;

class RestaurantProductService
{
    public function create(array $data): RestaurantProduct
    {
      

        return RestaurantProduct::create($data);
    }

    public function get()
    {
        $restaurantProducts = RestaurantProduct::get();
        return $restaurantProducts;
    }
}
