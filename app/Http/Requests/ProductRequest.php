<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_code' => ['required', 'string'],
            'product_name' => ['required', 'string'],
            'hs_code' => ['nullable', 'string'],
            'import_duty' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'facility' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max: 255'],
            'country_of_origin' => ['nullable', 'string', 'max: 255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
