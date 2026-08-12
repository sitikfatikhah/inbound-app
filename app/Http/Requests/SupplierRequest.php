<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
            'company_id' => ['required', 'integer'],
            'supplier_code' => ['required', 'string'],
            'supplier_name' => ['required', 'string'],
            'country' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string'],
            'email' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'incoterm_default' => ['nullable', 'string'],
            'currency' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
