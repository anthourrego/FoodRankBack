<?php

namespace App\Services;

use App\Models\RestaurantBranch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RestaurantBranchService
{
    /**
     * Obtener sucursales con filtros y paginación
     */
    public function getAllBranches(array $filters = [], int $perPage = 3): LengthAwarePaginator
    {
        $query = RestaurantBranch::with(['city', 'restaurant', 'createdBy', 'updatedBy']);

        $query = $this->applyFilters($query, $filters);

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Obtener una sucursal por ID
     */
    public function getBranchById(int $id): RestaurantBranch
    {
        return RestaurantBranch::with(['city', 'restaurant', 'createdBy', 'updatedBy'])
            ->findOrFail($id);
    }

    /**
     * Crear una nueva sucursal
     */
    public function createBranch(array $data, int $userId): RestaurantBranch
    {
        DB::beginTransaction();

        try {

            $data['created_by'] = $userId;

            $branch = RestaurantBranch::create($data);

            $branch->load(['city', 'restaurant', 'createdBy']);

            DB::commit();

            Log::info('Sucursal creada exitosamente', [
                'branch_id' => $branch->id,
                'user_id' => $userId
            ]);

            return $branch;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear sucursal', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => $userId
            ]);

            throw $e;
        }
    }

    /**
     * Actualizar una sucursal existente
     */
    public function updateBranch(int $id, array $data, int $userId): RestaurantBranch
    {
        DB::beginTransaction();

        try {
            $branch = RestaurantBranch::findOrFail($id);

            $data['updated_by'] = $userId;

            $branch->update($data);

            $branch->load(['city', 'restaurant', 'updatedBy']);

            DB::commit();

            Log::info('Sucursal actualizada exitosamente', [
                'branch_id' => $branch->id,
                'user_id' => $userId
            ]);

            return $branch;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al actualizar sucursal', [
                'branch_id' => $id,
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => $userId
            ]);

            throw $e;
        }
    }

    /**
     * Eliminar una sucursal
     */
    public function deleteBranch(int $id, int $userId): bool
    {
        DB::beginTransaction();

        try {
            $branch = RestaurantBranch::findOrFail($id);

            $branchInfo = [
                'id' => $branch->id,
                'address' => $branch->address,
                'restaurant_id' => $branch->restaurant_id
            ];

            $branch->delete();

            DB::commit();

            Log::info('Sucursal eliminada exitosamente', [
                'branch_info' => $branchInfo,
                'user_id' => $userId
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al eliminar sucursal', [
                'branch_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            throw $e;
        }
    }

    /**
     * Cambiar el estado activo/inactivo de una sucursal
     */
    public function toggleBranchStatus(int $id, int $userId): RestaurantBranch
    {
        DB::beginTransaction();

        try {
            $branch = RestaurantBranch::findOrFail($id);

            $previousStatus = $branch->is_active;
            $branch->is_active = !$branch->is_active;
            $branch->updated_by = $userId;
            $branch->save();

            DB::commit();

            Log::info('Estado de sucursal actualizado', [
                'branch_id' => $branch->id,
                'previous_status' => $previousStatus,
                'new_status' => $branch->is_active,
                'user_id' => $userId
            ]);

            return $branch;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al cambiar estado de sucursal', [
                'branch_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            throw $e;
        }
    }

    /**
     * Obtener sucursales por restaurante
     */
    public function getBranchesByRestaurant(int $restaurantId, bool $activeOnly = false): Collection
    {
        $query = RestaurantBranch::with(['city'])
            ->where('restaurant_id', $restaurantId);

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->orderBy('address')->get();
    }

    /**
     * Obtener sucursales por ciudad
     */
    public function getBranchesByCity(int $cityId, bool $activeOnly = false): Collection
    {
        $query = RestaurantBranch::with(['restaurant'])
            ->where('city_id', $cityId);

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->orderBy('address')->get();
    }

    /**
     * Contar sucursales activas por restaurante
     */
    public function countActiveBranchesByRestaurant(int $restaurantId): int
    {
        return RestaurantBranch::where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Aplicar filtros a la consulta
     */
    private function applyFilters($query, array $filters)
    {
        if (isset($filters['restaurant_id']) && $filters['restaurant_id']) {
            $query->where('restaurant_id', $filters['restaurant_id']);
        }

        if (isset($filters['city_id']) && $filters['city_id']) {
            $query->where('city_id', $filters['city_id']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('address', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('restaurant', function ($query) use ($search) {
                      $query->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('city', function ($query) use ($search) {
                      $query->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (isset($filters['has_coordinates']) && $filters['has_coordinates']) {
            $query->whereNotNull('latitude')
                  ->whereNotNull('longitude');
        }

        return $query;
    }
}