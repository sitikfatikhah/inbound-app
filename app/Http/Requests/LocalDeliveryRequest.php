<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LocalDeliveryRequest extends FormRequest
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
            'delivery_number' => ['required', 'string', 'max:255'],
            'purchase_order_id' => ['nullable', 'string', 'max:50', 'unique:local_deliveries,delivery_code'],
            'promised_date' => ['nullable', 'date'],
            'ETD' => ['nullable', 'date'],
            'ETA' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:100'],
        ];
    }
}
