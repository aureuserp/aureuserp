<?php

return [
    'columns' => [
        'name'         => 'Name',
        'code'         => 'Code',
        'custodian'    => 'Warehouse Keeper',
        'assets-count' => 'Stored Assets',
        'total-cost'   => 'Total Valuation',
        'is-active'    => 'Active',
        'company'      => 'Company',
        'creator'      => 'Created By',
        'created-at'   => 'Created At',
        'updated-at'   => 'Updated At',
    ],

    'notification' => [
        'completed' => 'Your warehouse export has completed and :count row(s) exported.',
        'failed'    => ':count row(s) failed to export.',
    ],
];
