<?php

namespace App\Http\Requests\RestaurantProduct;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRestaurantProduct extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'description' => $this->input('description') ?? "",
        ]);
    }

    public function rules(): array
    {
        $productId = $this->route('product');
        $restaurantId = $this->input('restaurant_id');

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:150',
                Rule::unique('restaurant_products')
                    ->where('restaurant_id', $restaurantId)
                    ->ignore($productId)
            ],
            'description' => 'required|string|min:10',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'is_active' => 'boolean',
            'restaurant_id' => 'required|exists:restaurants,id'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre no puede exceder 150 caracteres',
            'name.unique' => 'Ya existe un producto con este nombre para este restaurante',
            'description.required' => 'La descripción es obligatoria',
            'description.min' => 'La descripción debe tener al menos 10 caracteres',
            'image.image' => 'El archivo debe ser una imagen',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, jpg, png, gif o webp',
            'image.max' => 'La imagen no puede exceder 5MB',
            'restaurant_id.required' => 'El restaurante es obligatorio',
            'restaurant_id.exists' => 'El restaurante seleccionado no existe',
        ];
    }
}
