<?php

return [
    'types' => [
        'invoice' => 'Invoice',
        'packing_list' => 'Packing List',
        'bill_of_lading' => 'Bill of Lading',
        'pib' => 'PIB',
    ],
    'required_by_type' => [
        'import' => [
            'invoice',
            'packing_list',
            'bill_of_lading',
            'pib',
        ],
    ],
    'inspection_sla_hours' => 24,
];
