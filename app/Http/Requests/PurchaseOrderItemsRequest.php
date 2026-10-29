<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderItemsRequest extends FormRequest
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

            'purchase_order_id' => [
                'required',
                'integer',
                'exists:purchase_orders,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */

            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'product_code' => [
                'required',
                'string',
                'max:100',
            ],

            'product_name' => [
                'required',
                'string',
                'max:255',
            ],

            'hs_code' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */

            'quantity' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'unit' => [
                'nullable',
                'string',
                'max:100',
            ],

            'facility' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Import / Customs
            |--------------------------------------------------------------------------
            */

            'import_duty' => [
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'exchange_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            /*
            |--------------------------------------------------------------------------
            | Price
            |--------------------------------------------------------------------------
            |
            | total_price digunakan.
            | unit_price TIDAK digunakan.
            |
            */

            'total_price' => [
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

            'purchase_order_id.required' => 'Purchase order ID is required.',

            'purchase_order_id.integer' => 'Purchase order ID must be an integer.',

            'purchase_order_id.exists' => 'The selected purchase order does not exist.',

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */

            'product_id.required' => 'Product is required.',

            'product_id.integer' => 'Product ID must be an integer.',

            'product_id.exists' => 'The selected product does not exist.',

            'product_code.required' => 'Product code is required.',

            'product_code.string' => 'Product code must be a string.',

            'product_code.max' => 'Product code must not exceed 100 characters.',

            'product_name.required' => 'Product name is required.',

            'product_name.string' => 'Product name must be a string.',

            'product_name.max' => 'Product name must not exceed 255 characters.',

            'hs_code.string' => 'HS Code must be a string.',

            'hs_code.max' => 'HS Code must not exceed 100 characters.',

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */

            'quantity.required' => 'Quantity is required.',

            'quantity.numeric' => 'Quantity must be a number.',

            'quantity.min' => 'Quantity must be greater than 0.',

            /*
            |--------------------------------------------------------------------------
            | Unit
            |--------------------------------------------------------------------------
            */

            'unit.string' => 'Unit must be a string.',

            'unit.max' => 'Unit must not exceed 100 characters.',

            /*
            |--------------------------------------------------------------------------
            | Facility
            |--------------------------------------------------------------------------
            */

            'facility.string' => 'Facility must be a string.',

            'facility.max' => 'Facility must not exceed 255 characters.',

            /*
            |--------------------------------------------------------------------------
            | Import Duty
            |--------------------------------------------------------------------------
            */

            'import_duty.numeric' => 'Import duty must be a number.',

            'import_duty.min' => 'Import duty cannot be negative.',

            'import_duty.decimal' => 'Import duty may have a maximum of 2 decimal places.',

            /*
            |--------------------------------------------------------------------------
            | Exchange Rate
            |--------------------------------------------------------------------------
            */

            'exchange_rate.numeric' => 'Exchange rate must be a number.',

            'exchange_rate.min' => 'Exchange rate cannot be negative.',

            'exchange_rate.decimal' => 'Exchange rate may have a maximum of 2 decimal places.',

            /*
            |--------------------------------------------------------------------------
            | Total Price
            |--------------------------------------------------------------------------
            */

            'total_price.numeric' => 'Total price must be a number.',

            'total_price.min' => 'Total price cannot be negative.',

            'total_price.decimal' => 'Total price may have a maximum of 2 decimal places.',
        ];
    }
}
