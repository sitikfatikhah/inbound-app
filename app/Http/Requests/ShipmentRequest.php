<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class ShipmentRequest extends FormRequest
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

            'shipment_number' => [
                'required',
                'string',
                'max:50',
            ],

            'company_id' => [
                'required',
                'exists:companies,id',
            ],

            'supplier_id' => [
                'required',
                'exists:suppliers,id',
            ],

            'purchase_order_id' => [
                'nullable',
                'exists:purchase_orders,id',
            ],

            'shipping_line_id' => [
                'nullable',
                'exists:shipping_lines,id',
            ],

            'vessel_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'voyage_no' => [
                'nullable',
                'string',
                'max:50',
            ],

            'eta' => [
                'nullable',
                'date',
            ],

            'etd' => [
                'nullable',
                'date',
            ],

            'status' => [
                'required',
                'in:Draft,Booking,On Sea,Arrival,Custom Clearance,Warehouse,Completed,Cancelled',
            ],

        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'shipment_number.required' => 'Shipment Number wajib diisi',
            'suppier_id.required' => 'Supplier wajib dipilih',
            'company_id.required' => 'Company wajib  dipilih',
            'eta.date' => 'ETA harus berupa tanggal',
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'shipment_number' => 'Shipment Number',
            'supplier_id' =>'Supplier',
            'company_id' => 'Company',
            'eta' => 'ETA',
        ];
    }
}
