<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // get ID from route model binding
        $id = $this->route('product_attribute')?->id;

        return [
            'name' => 'required|string|max:191',
            'slug' => 'required|string|max:191|unique:product_attributes,slug,' . $id,
            'status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'The slug has already been taken.',
        ];
    }
}
