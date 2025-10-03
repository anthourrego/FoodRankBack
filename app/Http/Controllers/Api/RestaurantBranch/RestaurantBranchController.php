<?php

namespace App\Http\Controllers\Api\RestaurantBranch;

use App\Http\Controllers\Controller;
use App\Services\RestaurantBranchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RestaurantBranch\StoreRestaurantBranch;

class RestaurantBranchController extends Controller
{
    protected $branchService;

    /**
     * Inyectar el servicio en el controlador
     */
    public function __construct(RestaurantBranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'restaurant_id' => $request->get('restaurant_id'),
                'city_id' => $request->get('city_id'),
                'is_active' => $request->get('is_active'),
                'search' => $request->get('search'),
                'has_coordinates' => $request->get('has_coordinates'),
            ];

            $perPage = $request->get('per_page', 15);
            $branches = $this->branchService->getAllBranches($filters, $perPage);

            return response()->json($branches, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las sucursales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRestaurantBranch $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $branch = $this->branchService->createBranch($data, Auth::id());

            return response()->json([
                'message' => 'Sucursal creada exitosamente',
                'data' => $branch
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la sucursal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $branch = $this->branchService->getBranchById($id);
            return response()->json($branch, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sucursal no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRestaurantBranch $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();

            $branch = $this->branchService->updateBranch($id, $data, Auth::id());

            return response()->json([
                'message' => 'Sucursal actualizada exitosamente',
                'data' => $branch
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la sucursal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->branchService->deleteBranch($id, Auth::id());

            return response()->json([
                'message' => 'Sucursal eliminada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la sucursal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the active status of a branch.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $branch = $this->branchService->toggleBranchStatus($id, Auth::id());

            return response()->json([
                'message' => 'Estado actualizado exitosamente',
                'data' => $branch
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el estado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener sucursales por restaurante
     */
    public function getByRestaurant(string $restaurantId): JsonResponse
    {
        try {
            $activeOnly = request()->get('active_only', false);
            $branches = $this->branchService->getBranchesByRestaurant($restaurantId, $activeOnly);

            return response()->json([
                'data' => $branches,
                'count' => $branches->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las sucursales del restaurante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener sucursales por ciudad
     */
    public function getByCity(string $cityId): JsonResponse
    {
        try {
            $activeOnly = request()->get('active_only', false);
            $branches = $this->branchService->getBranchesByCity($cityId, $activeOnly);

            return response()->json([
                'data' => $branches,
                'count' => $branches->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las sucursales de la ciudad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de sucursales por restaurante
     */
    public function getStatsByRestaurant(string $restaurantId): JsonResponse
    {
        try {
            $activeCount = $this->branchService->countActiveBranchesByRestaurant($restaurantId);
            $branches = $this->branchService->getBranchesByRestaurant($restaurantId);

            return response()->json([
                'total' => $branches->count(),
                'active' => $activeCount,
                'inactive' => $branches->count() - $activeCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}