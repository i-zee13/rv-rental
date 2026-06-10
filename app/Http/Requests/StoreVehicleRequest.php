<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize()
    {
        // Admin middleware ensures authorization; allow here
        return true;
    }

    public function rules()
    {
        return [
            'category_id' => 'nullable|integer|exists:vehicle_categories,id',
            'make' => 'required|string|max:191',
            'model' => 'required|string|max:191',
            'year' => 'nullable|string|max:10',
            'price_per_day' => 'required|numeric|min:0',
            'seats' => 'nullable|integer|min:1|max:20',
            'bags' => 'nullable|integer|min:0|max:20',
            'status' => 'nullable|in:available,unavailable,maintenance,booked,hidden',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ];
    }
}
