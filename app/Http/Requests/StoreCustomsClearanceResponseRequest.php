<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomsClearanceResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'customs_response_type_id' => ['required', 'integer', 'exists:customs_response_types,id'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'response_date' => ['required', 'date'],
            'action_required' => ['nullable', 'string', 'max:5000'],
            'action_taken' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240'],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
