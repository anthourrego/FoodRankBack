<?php

namespace src\admin\Configuration\Infrastructure\Validators;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigurationRequest extends FormRequest
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
            'value' => 'required|string',
            'type' => 'required|string|max:255|in:text,textarea,image,boolean,number,banner',
            'description' => 'required|string|max:255',
        ];
    }
}
