<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Simpan activity log.
     */
    public function store(Request $request): JsonResponse
    {
        $activity = ActivityLog::create([
            'user_id' => Auth::id(),

            'module' => $request->module,
            'model_type' => $request->model_type,
            'model_id' => $request->model_id,

            'action' => strtoupper($request->action),

            'reference_no' => $request->reference_no,
            'description' => $request->description,

            'old_values' => $request->old_values,
            'new_values' => $request->new_values,

            'browser' => $request->userAgent(),
            'platform' => php_uname('s'),

            'ip_address' => $request->ip(),
            'url' => $request->fullUrl(),
            'http_method' => $request->method(),
            'session_id' => $request->session()->getId(),

            'status' => 'success',
        ]);

        return response()->json([
            'success' => true,
            'data' => $activity,
        ]);
    }
}
