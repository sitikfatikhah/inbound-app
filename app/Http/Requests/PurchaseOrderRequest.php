<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderRequest extends FormRequest
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
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'po_number' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:50'],
            'po_date' => ['required', 'date'],
            'currency' => ['nullable', 'string', 'max:10'],
            'exchange_rate' => ['nullable', 'numeric', 'decimal:0,2'],
            'incoterm' => ['nullable', 'string', 'max:20'],
            'payment_term' => ['nullable', 'string', 'max:100'],
            'delivery_date' => ['nullable', 'date'],
            'arrival_date' => ['nullable', 'date'],
            'status' => [
                'nullable',
                'string',
                'max:50',
                'in:DRAFT,BOOKED,IN_TRANSIT,ARRIVED_PORT,CUSTOMS_PROCESS,CUSTOMS_CLEARED,DO_RELEASED,DELIVERY_PROCESS,DELIVERED,COMPLETED,ON_HOLD,CANCELLED',
            ],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'min: 0.01',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min: 0',
                'decimal: 0,2',
            ],
            'items.*.total_price' => [
                'required',
                'numeric',
                'min: 0',
                'decimal:0,2',
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'po_number.required' => 'PO Number is required.',
            'po_number.string' => 'PO Number must be a string.',
            'po_number.max' => 'PO Number may not be greater than 100 characters.',
            'supplier_id.string' => 'Supplier must be a string.',
            'supplier_id.exists' => 'Selected supplier does not exist.',
            'po_date.required' => 'PO Date is required.',
            'po_date.date' => 'PO Date must be a valid date.',
            'currency.nullable' => 'Currency is optional.',
            'currency.string' => 'Currency must be a string.',
            'currency.max' => 'Currency may not be greater than 10 characters.',
            'exchange_rate.decimal' => 'Exchange Rate must be a decimal number.',
            'incoterm.string' => 'Incoterm must be a string.',
            'incoterm.max' => 'Incoterm may not be greater than 20 characters.',
            'payment_term.string' => 'Payment Term must be a string.',
            'payment_term.max' => 'Payment Term may not be greater than 100 characters.',
            'delivery_date.date' => 'Delivery Date must be a valid date.',
            'arrival_date.date' => 'Arrival Date must be a valid date.',
            'status.string' => 'Status must be a string.',
            'status.max' => 'Status may not be greater than 50 characters.',
            'items.required' => 'At least one purchase order item is required.', 'items.array' => 'Purchase order items must be an array.', 'items.min' => 'At least one purchase order item is required.', 'items.*.product_id.required' => 'Product is required.', 'items.*.product_id.exists' => 'Selected product does not exist.', 'items.*.quantity.required' => 'Quantity is required.', 'items.*.quantity.numeric' => 'Quantity must be a number.', 'items.*.quantity.min' => 'Quantity must be greater than 0.', 'items.*.unit_price.required' => 'Unit price is required.', 'items.*.unit_price.numeric' => 'Unit price must be a number.', 'items.*.unit_price.min' => 'Unit price cannot be negative.', 'items.*.unit_price.decimal' => 'Unit price must have a maximum of 2 decimal places.', 'items.*.total_price.required' => 'Total price is required.', 'items.*.total_price.numeric' => 'Total price must be a number.', 'items.*.total_price.min' => 'Total price cannot be negative.', 'items.*.total_price.decimal' => 'Total price must have a maximum of 2 decimal places.',

        ];
    }
}
