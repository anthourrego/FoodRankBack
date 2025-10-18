<?php

namespace src\admin\Events\Infrastructure\Validators;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            "name" => "required|string|max:255",
            "description" => "required|string|max:255",
            "start_date" => "required|date",
            "end_date" => "required|date",
            "city_id" => "required|numeric|min:1"
        ];
    }

    public function attributes()
    {
        return [
            "name" => "nombre",
            "description" => "descripcion",
            "start_date" => "fecha inicial",
            "end_date" => "fecha final",
            "city_id" => "id de la ciudad"
        ];
    }
}
