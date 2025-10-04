<?php

namespace App\Services;

use App\Models\RestaurantProduct;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantProductService
{

  public function get(Request $request = null)
  {
    $query = RestaurantProduct::with(['restaurant', 'createdBy', 'updatedBy']);

    if ($request && $request->has('is_active')) {
        $query->where('is_active', $request->boolean('is_active'));
    }

    if ($request && $request->has('restaurant_id')) {
        $query->where('restaurant_id', $request->restaurant_id);
    }

    if ($request && $request->has('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    $sortBy = $request ? $request->get('sort_by', 'created_at') : 'created_at';
    $sortOrder = $request ? $request->get('sort_order', 'desc') : 'desc';
    $query->orderBy($sortBy, $sortOrder);

    $perPage = $request ? $request->get('per_page', 10) : 10;
    $products = $query->paginate($perPage);

    return $products;
  }

  public function getRestaurants()
  {
    $restaurants = Restaurant::select('id', 'name')
        ->where('is_active', true)
        ->orderBy('name')
        ->get();
    return $restaurants;
  }

  public function create($data)
  {
    $data['created_by'] = Auth::id();

    $product = RestaurantProduct::create($data);
    $product->load(['restaurant', 'createdBy']);
    return $product;
  }

  public function update($data, $id)
  {
    $data['updated_by'] = Auth::id();

    $product = RestaurantProduct::find($id);
    $product->update($data);
    $product->load(['restaurant', 'createdBy', 'updatedBy']);
    return $product;
  }

  public function delete($id)
  {
    RestaurantProduct::find($id)->delete();
  }

  public function toggleStatus($id)
  {
    $product = RestaurantProduct::find($id);
    $product->update([
        'is_active' => !$product->is_active,
        'updated_by' => Auth::id()
    ]);
    $product->load(['restaurant', 'createdBy', 'updatedBy']);
    return $product;
  }

}
