<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\StoreResturant;
use App\Models\City;
use App\Models\Restaurant;
use App\Services\RestaurantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{

    protected RestaurantService $restaurantService;
    public function __construct(RestaurantService $restaurantService)
    {
        $this->restaurantService = $restaurantService;
    }

    public function index(Request $request): JsonResponse
    {
        $restaurants = $this->restaurantService->get($request);

        return response()->json($restaurants);
    }

    public function getCities(): JsonResponse
    {
        $cities = $this->restaurantService->getCities();
        return response()->json($cities);
    }

    public function store(StoreResturant $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $existeName = Restaurant::where('name', $data['name'])->first();
            if ($existeName) {
                return response()->json([
                    'message' => 'El nombre ya esta almacenado',
                ], 500);
            }

            $restaurant = $this->restaurantService->create($data);

            return response()->json([
                'message' => 'Restaurant created successfully',
                'restaurant' => $restaurant
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating restaurant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(StoreResturant $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();

            $existeName = Restaurant::where('name', $data['name'])->where('id', '<>', $id)->first();
            if ($existeName) {
                return response()->json([
                    'message' => 'El nombre ya esta almacenado',
                ], 500);
            }

            $restaurant = $this->restaurantService->update($data, $id);
            
            return response()->json([
                'message' => 'Restaurant updated successfully',
                'restaurant' => $restaurant
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating restaurant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Restaurant $restaurant): JsonResponse
    {
        try {
            $this->restaurantService->delete($restaurant->id);
            
            return response()->json([
                'message' => 'Restaurant deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting restaurant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus($id): JsonResponse
    {
        try {
            $restaurant = $this->restaurantService->toggleStatus($id);

            return response()->json([
                'message' => 'Restaurant status updated successfully',
                'restaurant' => $restaurant
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating restaurant status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}