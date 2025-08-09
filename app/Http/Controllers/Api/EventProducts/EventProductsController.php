<?php


namespace App\Http\Controllers\Api\EventProducts;

use App\Http\Controllers\Controller;
use App\Models\EventProduct;
use App\Services\EventProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class EventProductsController extends Controller
{
    protected EventProductService $eventProductService;
    public function __construct(EventProductService $eventProductService)
    {
        $this->eventProductService = $eventProductService;
    }


    public function index(Request $request):JsonResponse
    {

        $restaurantProduct = $this->eventProductService->get();
        return response()->json($restaurantProduct,200);
    }

}
