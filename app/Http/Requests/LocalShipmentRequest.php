<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocalShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $shipment = $this->route('local_shipment');

        return [
            'shipment_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('local_shipments', 'shipment_number')->ignore($shipment),
            ],
            'purchase_orders_id' => [
                'required',
                'integer',
                Rule::exists('purchase_orders', 'id')
                    ->where(fn ($query) => $query->whereRaw('LOWER(category) = ?', ['local'])),
            ],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'delivery_order_number' => ['nullable', 'string', 'max:255'],
            'etd' => ['nullable', 'date'],
            'atd' => ['nullable', 'date'],
            'status_document' => ['required', 'string', 'max:255'],
            'tracking_status' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'milestones' => ['sometimes', 'array'],
            'milestones.*.milestone_name' => ['required', 'string', 'max:255'],
            'milestones.*.status' => ['required', 'in:Planned,In Progress,Completed'],
            'milestones.*.planned_date' => ['nullable', 'date'],
            'milestones.*.actual_date' => ['nullable', 'date'],
            'milestones.*.remarks' => ['nullable', 'string'],
        ];
    }
}
