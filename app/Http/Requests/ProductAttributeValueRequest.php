<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Adjust if you have permission logic
        return true;
    }

    public function rules(): array
    {
        // Detect model from route binding (works with resource controller)
        $value = $this->route('value') ?? $this->route('product_attribute_value');

        return [
            'name' => ['required', 'string', 'max:191'], // value name
            'slug' => ['required', 'string', 'max:191', Rule::unique('product_attribute_values', 'slug')->ignore($value),],
            'status' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The value name field is required.',
            'slug.unique' => 'This slug has already been taken.',
        ];
    }
}
