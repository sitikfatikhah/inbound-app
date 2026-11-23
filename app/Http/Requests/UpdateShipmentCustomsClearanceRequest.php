<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateShipmentCustomsClearanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'jalur' => ['required', 'in:Hijau,Merah'],
            'pib_number' => ['required', 'string', 'max:100'],
            'pib_date' => ['required', 'date'],
            'inspection_start' => ['nullable', 'date'],
            'inspection_end' => ['nullable', 'date', 'after_or_equal:inspection_start'],
            'sppb_number' => ['nullable', 'string', 'max:100'],
            'sppb_date' => ['nullable', 'date', 'after_or_equal:pib_date'],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->input('inspection_end') && ! $this->input('inspection_start')) {
                    $validator->errors()->add('inspection_start', 'Inspection start is required when an end time is provided.');
                }
            },
        ];
    }
}
