<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CustomsPaymentRequest extends FormRequest
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
            'customs_clearance_id' => ['required', 'integer', 'exists:customs_clearances,id'],
            'customs_value' => ['nullable', 'numeric'],
            'import_duty' => ['nullable', 'numeric'],
            'import_value' => ['nullable', 'numeric'],
            'vat' => ['nullable', 'numeric'],
            'pph_22_import' => ['nullable', 'numeric'],
            'luxury_tax' => ['nullable', 'numeric'],
            'other_taxes' => ['nullable', 'numeric'],
            'total_import_duty_and_taxes' => ['nullable', 'numeric'],
            'payment_date' => ['nullable', 'date'],
            'billing_code' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'customs_clearance_id.required' => 'Customs clearance is required.',
        ];
    }

    public function attributes(): array
    {
        return [
            'customs_clearance_id' => 'Customs Clearance ID',
        ];
    }
}
