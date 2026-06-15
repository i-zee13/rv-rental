<?php

namespace App\Http\Requests;

class UpdatePropertyRequest extends StorePropertyRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['title_en'] = 'sometimes|required|string|max:255';
        $rules['address_line1'] = 'sometimes|required|string|max:255';

        return $rules;
    }
}
