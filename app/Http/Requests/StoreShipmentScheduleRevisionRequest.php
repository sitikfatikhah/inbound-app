<?php

namespace App\Http\Requests;

use App\Models\Shipments;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreShipmentScheduleRevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'eta' => ['nullable', 'date', 'required_without:etd'],
            'etd' => ['nullable', 'date', 'required_without:eta'],
            'revision_reason' => ['required', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $shipment = $this->route('shipment');

                if (! $shipment instanceof Shipments) {
                    return;
                }

                $hasChangedSchedule = collect(['eta', 'etd'])->contains(function (string $field) use ($shipment, $validator): bool {
                    if (! $this->filled($field) || $validator->errors()->has($field)) {
                        return false;
                    }

                    return Carbon::parse($this->input($field))->toDateString()
                        !== $shipment->{$field}?->toDateString();
                });

                if (! $hasChangedSchedule) {
                    $validator->errors()->add('eta', 'Enter a new ETA or ETD date to record a revision.');
                }
            },
        ];
    }
}
