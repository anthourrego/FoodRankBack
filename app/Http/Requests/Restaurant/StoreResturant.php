<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResturant extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'description' => $this->input('description') ?? "",
            'address' => $this->input('address') ?? "",
            'email' => $this->input('email') ? strtolower(trim($this->email)) : "",
            'phone' => $this->input('phone') ?? "",
            'website' => $this->input('website') ?? null,
            'instagram' => $this->input('instagram') ?? null,
            'facebook' => $this->input('facebook') ?? null,
        ]);
    }

    public function rules(): array
    {
        $restaurantId = $this->route('restaurant')?->id;
        return [
            'name' => ['required','string','min:3','max:255',Rule::unique('restaurants')->ignore($restaurantId)],
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:30|regex:/^[\d\s\+\-\(\)]+$/',
            'website' => 'nullable|url|max:100',
            'instagram' => 'nullable|string|max:100|regex:/^https?:\/\/(www\.)?instagram\.com\/[\w\.\-_]+\/?$/',
            'facebook' => 'nullable|string|max:100|regex:/^https?:\/\/(www\.)?facebook\.com\/[\w\.\-_]+\/?$/',
            'is_active' => 'boolean',
            'city_id' => 'required|exists:cities,id'
        ];
    }
}
