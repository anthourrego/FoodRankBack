<?php

namespace App\Http\Requests\RestaurantBranch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRestaurantBranch extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'address' => $this->input('address') ?? "",
            'phone' => $this->input('phone') ?? "",
            'latitude' => $this->input('latitude') ?? "",
            'longitude' => $this->input('longitude') ?? null,
            'city_id' => $this->input('city_id') ?? null,
            'restaurant_id' => $this->input('restaurant_id') ?? null,
        ]);
    }

    public function rules(): array
    {
        $restaurantId = $this->route('restaurant');
        return [
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:30|regex:/^[\d\s\+\-\(\)]+$/',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'city_id' => 'required|exists:cities,id',
            'restaurant_id' => 'required|exists:restaurants,id'
        ];
    }
}
