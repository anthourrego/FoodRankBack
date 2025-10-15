<?php

namespace src\admin\Configuration\Infrastructure\Validators;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreConfigurationRequest extends FormRequest
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
        $rules = [
            'key' => 'required|string|max:255',
            'type' => 'required|string|max:255|in:text,textarea,image,boolean,number,banner',
            'description' => 'required|string|max:255',
            'eventId' => 'required|exists:events,id',
        ];

        // Si el tipo es image, validar archivo en imageFile
        if ($this->input('type') === 'image') {
            $rules['imageFile'] = 'required|file|mimes:jpeg,jpg,png,webp,avif|max:5120'; // Incluir avif
            $rules['value'] = 'nullable|string'; // value es opcional para imágenes
        } else {
            $rules['value'] = 'required|string';
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('type') === 'image' && $this->hasFile('imageFile')) {
                $file = $this->file('imageFile');
                $extension = strtolower($file->getClientOriginalExtension());
                $mimeType = $file->getMimeType();
                
                // Debug: Log para ver qué está recibiendo
                \Log::info('Archivo recibido:', [
                    'nombre' => $file->getClientOriginalName(),
                    'extension' => $extension,
                    'mime_type' => $mimeType,
                    'size' => $file->getSize(),
                    'is_valid' => $file->isValid(),
                    'error' => $file->getError()
                ]);
                
                // Validación manual adicional
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
                
                if (!in_array($extension, $allowedExtensions) && !in_array($mimeType, $allowedMimeTypes)) {
                    $validator->errors()->add('imageFile', "Tipo de archivo no permitido. Extensión: {$extension}, MIME: {$mimeType}");
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'value.required' => 'El valor es requerido',
            'imageFile.required' => 'La imagen es requerida cuando el tipo es image',
            'imageFile.file' => 'El campo imageFile debe ser un archivo',
            'imageFile.mimes' => 'El archivo debe ser de tipo: jpeg, jpg, png, webp, avif',
            'imageFile.max' => 'El archivo no debe ser mayor a 5MB',
        ];
    }
}
