<?php

namespace Database\Seeders;

use App\Models\TrackingStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TrackingStatusSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'customs' => [
                'PL & INV created',
                'BL Draft',
                'BL Final',
                'LCC created',
                'PTDI',
                'sent document',
                'draft PIB',
                'Billing Bea Cukai',
                'BPN',
                'customs clearance',
                'Green Lane - SPPB',
                'Red Lane - SPJM',
                'NPD',
                'NHI',
                'SPTNP',
                'SPKTNP',
                'SPBL',
                'Gate Out',
                'Others',
            ],

            'shipment' => [
                'not shipped',
                'gate out empty',
                'gate in',
                'loaded on vessel',
                'vessel departed',
                'in transit',
                'vessel arrived',
                'discharged',
                'delivered',
                'gate out for delivery',
                'empty container return',
                'completed',
                'on hold',
                'cancelled',
            ],
        ];

        foreach ($groups as $category => $statuses) {
            foreach ($statuses as $index => $name) {
                $code = Str::slug($name, '_');

                TrackingStatus::updateOrCreate(
                    [
                        'category' => $category,
                        'code' => $code,
                    ],
                    [
                        'name' => $name,
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
