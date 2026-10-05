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
            'purchase_order_id' => ['required', 'integer', 'exists:purchase_orders,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_code' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'hs_code' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:100'],
            'facility' => ['nullable', 'string', 'max:255'],
            'import_duty' => ['nullable', 'numeric', 'min:0'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'total_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
    public function messages(): array
    {
        return [
            'purchase_order_id.required' => 'Purchase order ID is required.',
            'purchase_order_id.integer' => 'Purchase order ID must be an integer.',
            'purchase_order_id.exists' => 'The selected purchase order does not exist.',
            'product_id.required' => 'Product ID is required.',
            'product_id.integer' => 'Product ID must be an integer.',
            'product_id.exists' => 'The selected product does not exist.',
            'hs_code.required' => 'HS Code is required.',
            'hs_code.string' => 'HS Code must be a string.',
            'hs_code.max' => 'HS Code must not exceed 255 characters.',
            'quantity.required' => 'Quantity is required.',
            'quantity.numeric' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be a positive number.',
            'unit.nullable' => 'Unit is optional.',
            'unit.string' => 'Unit must be a string.',
            'unit.max' => 'Unit must not exceed 100 characters.',
            // Add more custom messages for other fields as needed
        ];
    }
}
