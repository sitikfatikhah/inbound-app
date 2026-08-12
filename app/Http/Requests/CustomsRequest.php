<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CustomsRequest extends FormRequest
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
            'shipment_id' => ['required', 'string'],
            'pib_number' => ['nullable', 'string'],
            'pib_date' => ['nullable', 'date'],
            'nopen' => ['nullable', 'string'],
            'sppb_number' => ['nullable', 'string'],
            'sppb_date' => ['nullable', 'date'],
            'routing_status' => ['nullable', 'string'],
            'inspection_status' => ['nullable', 'string'],
            'inspection_date' => ['nullable', 'date'],
            'customs_clearance_date' => ['nullable', 'date'],
            'customs_clearance_status' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return[
            'shipment_id.required' => 'Shipment Number wajib diisi.'
        ];
    }

    public function attributes(): array
    {
        return [
            'shipment_id' => 'Shipment ID'
        ];
    }
}
