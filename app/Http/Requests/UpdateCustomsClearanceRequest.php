<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomsClearanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'pib_number' => ['required', 'string', 'max:255'],
            'pib_date' => ['required', 'date'],
            'customs_channel' => ['nullable', 'in:Green Line,Red Line'],
            'customs_status' => ['nullable', 'string', 'max:255'],
            'inspection_start' => ['nullable', 'date'],
            'inspection_end' => ['nullable', 'date', 'after_or_equal:inspection_start'],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
