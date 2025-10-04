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
            'image_url' => $this->input('image_url') ?? "",
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
            'image_url' => 'nullable|url|max:255',
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
            'image_url.url' => 'La URL de la imagen no es válida',
            'restaurant_id.required' => 'El restaurante es obligatorio',
            'restaurant_id.exists' => 'El restaurante seleccionado no existe',
        ];
    }
}
