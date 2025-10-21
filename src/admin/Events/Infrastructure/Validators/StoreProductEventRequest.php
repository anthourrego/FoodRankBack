<?php

namespace src\admin\Events\Infrastructure\Validators;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "product_id" => "required|numeric|min:1|exists:restaurant_products,id",
        ];
    }

    public function attributes()
    {
        return [
            "product_id" => "id del producto"
        ];
    }
}
