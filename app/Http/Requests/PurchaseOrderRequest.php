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
            /*
            |--------------------------------------------------------------------------
            | Purchase Order
            |--------------------------------------------------------------------------
            */

            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
            ],

            'po_number' => [
                'required',
                'string',
                'max:100',
            ],

            'category' => [
                'required',
                'string',
                'max:50',
            ],

            'po_date' => [
                'required',
                'date',
            ],

            'currency' => [
                'nullable',
                'string',
                'max:10',
            ],

            'exchange_rate' => [
                'nullable',
                'numeric',
                'decimal:0,2',
            ],

            'incoterm' => [
                'nullable',
                'string',
                'max:20',
            ],

            'payment_term' => [
                'nullable',
                'string',
                'max:100',
            ],

            'delivery_date' => [
                'nullable',
                'date',
            ],

            'arrival_date' => [
                'nullable',
                'date',
            ],

            'status' => [
                'nullable',
                'string',
                'max:50',
                'in:DRAFT,BOOKED,IN_TRANSIT,ARRIVED_PORT,CUSTOMS_PROCESS,CUSTOMS_CLEARED,DO_RELEASED,DELIVERY_PROCESS,DELIVERED,COMPLETED,ON_HOLD,CANCELLED',
            ],

            /*
            |--------------------------------------------------------------------------
            | Purchase Order Items
            |--------------------------------------------------------------------------
            */

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'items.*.product_code' => [
                'nullable',
                'string',
                'max:100',
            ],

            'items.*.product_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'items.*.hs_code' => [
                'nullable',
                'string',
                'max:100',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'items.*.unit' => [
                'nullable',
                'string',
                'max:100',
            ],

            'items.*.facility' => [
                'nullable',
                'string',
                'max:100',
            ],

            'items.*.import_duty' => [
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'items.*.exchange_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Purchase Order
            |--------------------------------------------------------------------------
            */

            'supplier_id.required' => 'Supplier is required.',
            'supplier_id.integer' => 'Supplier must be a valid ID.',
            'supplier_id.exists' => 'Selected supplier does not exist.',

            'po_number.required' => 'PO Number is required.',
            'po_number.string' => 'PO Number must be a string.',
            'po_number.max' => 'PO Number may not be greater than 100 characters.',

            'category.string' => 'Category must be a string.',
            'category.required' => 'Category is required.',
            'category.max' => 'Category may not be greater than 50 characters.',

            'po_date.required' => 'PO Date is required.',
            'po_date.date' => 'PO Date must be a valid date.',

            'currency.string' => 'Currency must be a string.',
            'currency.max' => 'Currency may not be greater than 10 characters.',

            'exchange_rate.numeric' => 'Exchange Rate must be a number.',
            'exchange_rate.decimal' => 'Exchange Rate may have a maximum of 2 decimal places.',
            'exchange_rate.min' => 'Exchange Rate cannot be negative.',

            'incoterm.string' => 'Incoterm must be a string.',
            'incoterm.max' => 'Incoterm may not be greater than 20 characters.',

            'payment_term.string' => 'Payment Term must be a string.',
            'payment_term.max' => 'Payment Term may not be greater than 100 characters.',

            'delivery_date.date' => 'Delivery Date must be a valid date.',
            'arrival_date.date' => 'Arrival Date must be a valid date.',

            'status.string' => 'Status must be a string.',
            'status.max' => 'Status may not be greater than 50 characters.',
            'status.in' => 'Selected status is invalid.',

            /*
            |--------------------------------------------------------------------------
            | Items
            |--------------------------------------------------------------------------
            */

            'items.required' => 'At least one purchase order item is required.',
            'items.array' => 'Purchase order items must be an array.',
            'items.min' => 'At least one purchase order item is required.',

            'items.*.product_id.required' => 'Product is required.',
            'items.*.product_id.integer' => 'Product ID must be valid.',
            'items.*.product_id.exists' => 'Selected product does not exist.',

            'items.*.product_code.string' => 'Product code must be a string.',
            'items.*.product_code.max' => 'Product code may not be greater than 100 characters.',

            'items.*.product_name.string' => 'Product name must be a string.',
            'items.*.product_name.max' => 'Product name may not be greater than 100 characters.',

            'items.*.hs_code.string' => 'HS Code must be a string.',
            'items.*.hs_code.max' => 'HS Code may not be greater than 100 characters.',

            'items.*.quantity.required' => 'Quantity is required.',
            'items.*.quantity.numeric' => 'Quantity must be a number.',
            'items.*.quantity.min' => 'Quantity must be greater than 0.',

            'items.*.unit_price.required' => 'Unit price is required.',
            'items.*.unit_price.numeric' => 'Unit price must be a number.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
            'items.*.unit_price.decimal' => 'Unit price may have a maximum of 2 decimal places.',

            'items.*.unit.string' => 'Unit must be a string.',
            'items.*.unit.max' => 'Unit may not be greater than 100 characters.',

            'items.*.facility.string' => 'Facility must be a string.',
            'items.*.facility.max' => 'Facility may not be greater than 100 characters.',

            'items.*.import_duty.numeric' => 'Import duty must be a number.',
            'items.*.import_duty.min' => 'Import duty cannot be negative.',
            'items.*.import_duty.decimal' => 'Import duty may have a maximum of 2 decimal places.',

            'items.*.exchange_rate.numeric' => 'Item exchange rate must be a number.',
            'items.*.exchange_rate.min' => 'Item exchange rate cannot be negative.',
            'items.*.exchange_rate.decimal' => 'Item exchange rate may have a maximum of 2 decimal places.',
        ];
    }
}
