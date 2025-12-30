<?php

namespace App\Services;

use App\Models\RestaurantProduct;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    if (isset($data['image'])) {
      $data['image_url'] = $this->uploadImage($data['image']);
      unset($data['image']);
    }

    $product = RestaurantProduct::create($data);
    $product->load(['restaurant', 'createdBy']);
    return $product;
  }

  public function update($data, $id)
  {
    $data['updated_by'] = Auth::id();

    $product = RestaurantProduct::find($id);

    if (isset($data['image'])) {
      if ($product->image_url) {
        $this->deleteImage($product->image_url);
      }
      $data['image_url'] = $this->uploadImage($data['image']);
      unset($data['image']);
    }

    $product->update($data);
    $product->load(['restaurant', 'createdBy', 'updatedBy']);
    return $product;
  }

  public function delete($id)
  {
    $product = RestaurantProduct::find($id);

    if ($product->image_url) {
      $this->deleteImage($product->image_url);
    }

    $product->delete();
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

  private function uploadImage($image)
  {
    if (!$image) {
      return null;
    }

    $fileName = Str::uuid() . '.' . $image->getClientOriginalExtension();
    $image->storeAs('images/products', $fileName, 'public');

    return $fileName;
  }

  private function deleteImage($imageUrl)
  {
    if (!$imageUrl) {
      return;
    }

    $path = 'images/products/' . $imageUrl;

    if (Storage::disk('public')->exists($path)) {
      Storage::disk('public')->delete($path);
    }
  }

}
