<?php

return [
    'columns' => [
        'occurred-at'        => 'Date & Time',
        'asset-tag'          => 'Asset Tag',
        'product'            => 'Product',
        'model'              => 'Model',
        'category'           => 'Category',
        'serial-no'          => 'Serial Number',
        'type'               => 'Movement Type',
        'quantity'           => 'Quantity',
        'source-type'        => 'Source Type',
        'from'               => 'From',
        'to'                 => 'To',
        'condition'          => 'Condition',
        'expected-return-at' => 'Expected Return At',
        'performed-by'       => 'Performed By',
        'note'               => 'Note',
        'company'            => 'Company',
    ],

    'options' => [
        'section'    => 'Filter Exported Movements (Optional)',
        'date-from'  => 'From Date',
        'date-until' => 'Until Date',
        'types'      => 'Movement Types',
        'sites'      => 'Sites',
        'employees'  => 'Employees',
    ],

    'notification' => [
        'completed' => 'Your asset movement export has completed and :count row(s) exported.',
        'failed'    => ':count row(s) failed to export.',
    ],
];
