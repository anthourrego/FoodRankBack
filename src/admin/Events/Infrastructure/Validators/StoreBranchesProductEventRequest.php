<?php

namespace src\admin\Events\Infrastructure\Validators;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchesProductEventRequest extends FormRequest
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
            "branch_ids" => "required|array|min:1",
            "branch_ids.*" => "required|numeric|min:1|exists:restaurant_branches,id",
        ];
    }

    public function attributes()
    {
        return [
            "branch_ids" => "id de las sucursales"
        ];
    }
}
