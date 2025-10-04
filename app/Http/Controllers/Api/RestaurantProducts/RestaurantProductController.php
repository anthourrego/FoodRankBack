<?php

namespace App\Http\Controllers\Api\RestaurantProducts;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestaurantProduct\StoreRestaurantProduct;
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

    public function index(Request $request): JsonResponse
    {
        $products = $this->restaurantProductService->get($request);
        return response()->json($products);
    }

    public function getRestaurants(): JsonResponse
    {
        $restaurants = $this->restaurantProductService->getRestaurants();
        return response()->json($restaurants);
    }

    public function store(StoreRestaurantProduct $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $existeName = RestaurantProduct::where('name', $data['name'])
                ->where('restaurant_id', $data['restaurant_id'])
                ->first();

            if ($existeName) {
                return response()->json([
                    'message' => 'Ya existe un producto con este nombre para este restaurante',
                ], 422);
            }

            $product = $this->restaurantProductService->create($data);

            return response()->json([
                'message' => 'Producto creado exitosamente',
                'product' => $product
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(StoreRestaurantProduct $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();

            $existeName = RestaurantProduct::where('name', $data['name'])
                ->where('restaurant_id', $data['restaurant_id'])
                ->where('id', '!=', $id)
                ->first();

            if ($existeName) {
                return response()->json([
                    'message' => 'Ya existe un producto con este nombre para este restaurante',
                ], 422);
            }

            $product = $this->restaurantProductService->update($data, $id);

            return response()->json([
                'message' => 'Producto actualizado exitosamente',
                'product' => $product
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(RestaurantProduct $product): JsonResponse
    {
        try {
            $this->restaurantProductService->delete($product->id);

            return response()->json([
                'message' => 'Producto eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus($id): JsonResponse
    {
        try {
            $product = $this->restaurantProductService->toggleStatus($id);

            return response()->json([
                'message' => 'Estado del producto actualizado exitosamente',
                'product' => $product
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el estado del producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
