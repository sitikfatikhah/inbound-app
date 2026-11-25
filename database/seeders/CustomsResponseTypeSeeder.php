<?php

namespace Database\Seeders;

use App\Models\CustomsResponseType;
use Illuminate\Database\Seeder;

class CustomsResponseTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => 'SPJM', 'name' => 'SPJM', 'description' => 'Surat Penetapan Jalur Merah'],
            ['code' => 'SPPB', 'name' => 'SPPB', 'description' => 'Surat Persetujuan Pengeluaran Barang'],
            ['code' => 'NPD', 'name' => 'NPD', 'description' => 'Nota Permintaan Dokumen'],
            ['code' => 'SPTNP', 'name' => 'SPTNP', 'description' => 'Surat Penetapan Tarif dan/atau Nilai Pabean'],
            ['code' => 'OTHER', 'name' => 'Other document response', 'description' => 'Other customs document response'],
        ] as $type) {
            CustomsResponseType::query()->updateOrCreate(
                ['code' => $type['code']],
                [...$type, 'is_active' => true],
            );
        }
    }
}
