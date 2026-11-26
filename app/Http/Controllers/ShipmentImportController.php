<?php

namespace App\Http\Controllers;

use App\Models\Shipments;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShipmentImportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:10240',
            ],
        ]);

        $file = $request->file('file');

        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return back()->withErrors([
                'file' => 'File tidak dapat dibaca.',
            ]);
        }

        $headers = fgetcsv($handle, 0, ',', '"', '\\');

        if (! $headers) {
            fclose($handle);

            return back()->withErrors([
                'file' => 'File CSV kosong.',
            ]);
        }

        $headers = array_map(
            fn ($header) => strtolower(
                trim(str_replace(' ', '_', $header))
            ),
            $headers
        );

        $requiredHeaders = [
            'shipment_number',
            'bl_number',
            'invoice_number',
            'vessel_name',
            'voyage_number',
            'etd',
            'eta',
        ];

        foreach ($requiredHeaders as $header) {
            if (! in_array($header, $headers)) {
                fclose($handle);

                return back()->withErrors([
                    'file' => "Kolom {$header} tidak ditemukan.",
                ]);
            }
        }

        $success = 0;
        $failed = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv(
            $handle,
            0,
            ',',
            '"',
            '\\'
        )) !== false) {

            $rowNumber++;

            if (count($row) !== count($headers)) {
                $failed++;

                $errors[] = [
                    'row' => $rowNumber,
                    'message' => 'Jumlah kolom tidak sesuai.',
                ];

                continue;
            }

            $data = array_combine($headers, $row);

            $validator = Validator::make($data, [
                'shipment_number' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'bl_number' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'invoice_number' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'vessel_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'voyage_number' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'etd' => [
                    'nullable',
                    'date_format:Y-m-d',
                ],

                'eta' => [
                    'nullable',
                    'date_format:Y-m-d',
                ],

                'remarks' => [
                    'nullable',
                    'string',
                ],
            ]);

            if ($validator->fails()) {
                $failed++;

                $errors[] = [
                    'row' => $rowNumber,
                    'message' => $validator->errors()->first(),
                ];

                continue;
            }

            try {
                DB::transaction(function () use ($data) {
                    Shipments::updateOrCreate(
                        [
                            'shipment_number' => $data['shipment_number'],
                        ],
                        [
                            'bl_number' => $data['bl_number'] ?: null,

                            'invoice_number' => $data['invoice_number'] ?: null,

                            'vessel_name' => $data['vessel_name'] ?: null,

                            'voyage_number' => $data['voyage_number'] ?: null,

                            'etd' => $data['etd'] ?: null,

                            'eta' => $data['eta'] ?: null,

                            'remarks' => $data['remarks'] ?: null,
                        ]
                    );
                });

                $success++;
            } catch (\Throwable $e) {
                report($e);

                $failed++;

                $errors[] = [
                    'row' => $rowNumber,
                    'message' => 'Gagal menyimpan data.',
                ];
            }
        }

        fclose($handle);

        return back()->with('import_result', [
            'success' => $success,
            'failed' => $failed,
            'errors' => array_slice($errors, 0, 50),
        ]);
    }
}
