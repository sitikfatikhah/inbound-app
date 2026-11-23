<?php

namespace App\Http\Requests;

use App\Models\ShipmentDocuments;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateShipmentDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'document_name' => ['sometimes', 'required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'document_date' => ['nullable', 'date'],
            'document_file' => ['sometimes', 'required', 'file', 'mimes:pdf,jpg,jpeg,png,xlsx,doc,docx', 'max:10240'],
            'verification_status' => ['sometimes', 'required', 'in:pending,complete,deficient'],
            'verification_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $document = $this->route('shipment_document');
                $isUploaded = $document instanceof ShipmentDocuments
                    && ($document->document_file !== null || $this->hasFile('document_file'));

                if ($this->input('verification_status') === 'complete' && ! $isUploaded) {
                    $validator->errors()->add('verification_status', 'Upload the document before marking it complete.');
                }

                if ($this->input('verification_status') === 'deficient' && ! $this->filled('verification_notes')) {
                    $validator->errors()->add('verification_notes', 'Describe what is missing or incorrect.');
                }
            },
        ];
    }
}
