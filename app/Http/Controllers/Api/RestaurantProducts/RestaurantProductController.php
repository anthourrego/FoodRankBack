<?php


namespace App\Http\Controllers\Api\RestaurantProduct;

use App\Http\Controllers\Controller;
use App\Models\RestaurantProduct;
use App\Services\RestaurantProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class RestaurantProductController extends Controller
{
    protected RestaurantProductService $restaurantProductService;
    public function __construct(RestaurantProductService $restaurantProductService)
    {
        $this->restaurantProductService = $restaurantProductService;
    }


    public function index(Request $request):JsonResponse
    {

        $restaurantProduct = $this->restaurantProductService->get();
        return response()->json($restaurantProduct,200);
    }

}
