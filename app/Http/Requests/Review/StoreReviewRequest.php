<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_product_id'        => 'required|exists:event_products,id',
            'event_product_branch_id' => 'nullable|exists:event_product_branches,id',
            'rating'                  => 'required|integer|min:1|max:5',
            'comment'                 => 'nullable|string|max:255',
            'latitude'                => 'nullable|numeric',
            'longitude'               => 'nullable|numeric',
            'ip'                      => 'nullable|ip',
            'deviceId'                => 'nullable|string|max:150',
            'fingerprint_device'      => 'nullable|string|max:3072',
            'is_active'               => 'nullable|boolean',
            'created_by'              => 'nullable|exists:users,id',
            'updated_by'              => 'nullable|exists:users,id',
        ];
    }
}
