<?php

namespace App\Http\Requests;

use App\Rules\UploadedImage;
use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_type_id' => 'nullable|exists:property_types,id',
            'address_line1' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:100',
            'bedrooms' => 'required|integer|min:0|max:20',
            'bathrooms' => 'required|numeric|min:0|max:20',
            'sqft' => 'nullable|integer|min:0',
            'price_per_month' => 'required|numeric|min:0',
            'price_per_week' => 'nullable|numeric|min:0',
            'price_per_night' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'cleaning_fee' => 'nullable|numeric|min:0',
            'max_guests' => 'nullable|integer|min:1',
            'min_nights' => 'nullable|integer|min:1',
            'pets_allowed' => 'nullable|boolean',
            'furnished' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'instant_book' => 'nullable|boolean',
            'status' => 'required|in:available,unavailable,rented,hidden',
            'available_from' => 'nullable|date',
            'title_en' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'title_es' => 'nullable|string|max:255',
            'description_es' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:50',
            'images' => 'nullable|array',
            'images.*' => ['nullable', 'file', 'max:4096', new UploadedImage],
        ];
    }
}
