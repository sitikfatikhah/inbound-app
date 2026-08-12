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
            'company_id'    => ['required', 'integer'],
            'po_number'     => ['required', 'string', 'max:100'],
            'supplier_id'   => ['required', 'integer'],
            'po_date'       => ['required', 'date'],
            'currency'      => ['required', 'string', 'max:10'],
            'exchange_rate' => ['nullable', 'numeric'],
            'incoterm'      => ['nullable', 'string', 'max:20'],
            'payment_term'  => ['nullable', 'string', 'max:100'],
            'delivery_date' => ['nullable', 'date'],
            'arrival_date'  => ['nullable', 'date'],
            'status'        => ['required', 'string', 'max:50'],
        ];
    }
}
