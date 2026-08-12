<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger implements ActivityLoggerInterface
{
    /**
     * Simpan activity log.
     */
    public function log(
        string $module,
        string $action,
        Model $model,
        array $old = [],
        array $new = [],
        ?string $description = null
    ): void {

        $request = request();

        ActivityLog::create([

            // Company
            'company_id' => Auth::user()?->company_id,

            // User
            'user_id' => Auth::id(),

            // Module
            'module' => $module,

            // CREATE UPDATE DELETE LOGIN dll
            'action' => strtoupper($action),

            // Model
            'model_type' => class_basename($model),

            'model_id' => $model->getKey(),

            // Nomor Referensi
            'reference_no' => $this->referenceNo($model),

            // Keterangan
            'description' => $description,

            // Sebelum Update
            'old_values' => empty($old)
                ? null
                : $old,

            // Sesudah Update
            'new_values' => empty($new)
                ? null
                : $new,

            // Browser
            'browser' => $request->userAgent(),

            // Platform
            'platform' => php_uname('s'),

            // IP
            'ip_address' => $request->ip(),

            // URL
            'url' => $request->fullUrl(),

            // HTTP Method
            'http_method' => $request->method(),

            // Session
            'session_id' => $request->session()->getId(),

            // Status
            'status' => 'success',

        ]);
    }

    /**
     * Mengambil nomor referensi dari model.
     */
    protected function referenceNo(Model $model): ?string
    {
        $references = [

            'shipment_number',

            'po_number',

            'supplier_code',

            'invoice_number',

            'container_number',

            'bl_number',

            'pib_number',

            'document_number',

            'code',

            'number',

            'name',

        ];

        foreach ($references as $column) {

            if (isset($model->{$column})) {

                return (string) $model->{$column};

            }
        }

        return null;
    }
}