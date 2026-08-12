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
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customs_clearance_id' => ['required', 'integer'],
            'customs_value' => ['nullable', 'decimal'],
            'import_duty' => ['nullable', 'decimal'],
            'import_value' => ['nullable', 'decimal'],
            'vat' => ['nullable', 'decimal'],
            'pph_22_import' => ['nullable', 'decimal'],
            'luxury_tax' => ['nullable', 'decimal'],
            'other_taxes' => ['nullable', 'decimal'],
            'total_import_duty_and_taxes' => ['nullable', 'decimal'],
            'payment_date' => ['nullable', 'date'],
            'billing_code' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'customs_clearance_id.required' => 'Shipment Number required',
            'suppier_id.required' => 'Supplier is required',
            'company_id.required' => 'Company is required',
            'eta.date' => 'ETA must be date',
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'customs_clearance_id' => 'Customs Clearance ID',
            'supplier_id' =>'Supplier',
            'company_id' => 'Company',
            'eta' => 'ETA',
        ];
    }
}
