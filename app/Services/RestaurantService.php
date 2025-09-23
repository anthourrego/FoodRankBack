<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\City;
use Illuminate\Support\Facades\Auth;

use function Illuminate\Log\log;

class RestaurantService
{

  public function get(Request $request)
  {
    $query = Restaurant::with(['city', 'createdBy', 'updatedBy']);

    if ($request->has('is_active')) {
        $query->where('is_active', $request->boolean('is_active'));
    }

    if ($request->has('city_id')) {
        $query->where('city_id', $request->city_id);
    }

    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('address', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    $sortBy = $request->get('sort_by', 'created_at');
    $sortOrder = $request->get('sort_order', 'desc');
    $query->orderBy($sortBy, $sortOrder);

    $perPage = $request->get('per_page', 15);
    $restaurants = $query->paginate($perPage);

    return $restaurants;
  }

  public function getCities()
  {
    $cities = City::select('id', 'name')->orderBy('name')->get();
    return $cities;
  }

  public function create($data)
  {
    $data['created_by'] = Auth::id();
    
    $restaurant = Restaurant::create($data);
    $restaurant->load(['city', 'createdBy']);
    return $restaurant;
  }

  public function update($data, $id)
  {
    $data['updated_by'] = Auth::id();

    Restaurant::find($id)->update($data);
    $restaurant->load(['city', 'createdBy', 'updatedBy']);
    return $restaurant;
  }

  public function delete($id)
  {
    Restaurant::find($id)->delete();
  }

  public function toggleStatus($id)
  {
    $restaurant = Restaurant::find($id);
    $restaurant->update([
        'is_active' => !$restaurant->is_active,
        'updated_by' => Auth::id()
    ]);
    return $restaurant;
  }

}
