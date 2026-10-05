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

            'forwarder_id' => [
                'nullable',
                'integer',
                'exists:forwarders,id',
            ],

            'shipping_line_id' => [
                'nullable',
                'integer',
                'exists:shipping_lines,id',
            ],

            'port_of_loading_id' => ['nullable', 'integer', 'exists:ports,id'],
            'port_of_discharge_id' => ['nullable', 'integer', 'exists:ports,id'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],

            'bl_number' => ['nullable', 'string', 'max:255'],

            'vessel_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'voyage_number' => [
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

            'atd' => ['nullable', 'date'],
            'ata' => ['nullable', 'date'],

            'status_document' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tracking_status' => ['nullable', 'string', 'max:255'],

            'location' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],

        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'shipment_number.required' => 'Shipment Number wajib diisi',
            'eta.date' => 'ETA harus berupa tanggal',
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'shipment_number' => 'Shipment Number',
            'forwarder_id' => 'Forwarder',
            'eta' => 'ETA',
        ];
    }
}
