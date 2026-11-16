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
            'purchase_order_id' => [
                'nullable',
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
            'items' => $this->isMethod('POST')
                ? ['required', 'array', 'min:1']
                : ['sometimes', 'array', 'min:1'],
            'items.*.purchase_order_id' => [
                'required',
                'integer',
                Rule::exists('purchase_orders', 'id')
                    ->where(fn ($query) => $query->whereRaw('LOWER(category) = ?', ['local'])),
            ],
            'items.*.purchase_order_item_id' => ['required', 'integer', 'exists:purchase_order_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
