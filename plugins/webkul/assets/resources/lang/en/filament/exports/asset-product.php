<?php

return [
    'columns' => [
        'name'                    => 'Product Name',
        'type'                    => 'Product Type',
        'category'                => 'Category',
        'model'                   => 'Model',
        'cost'                    => 'Cost',
        'assets-count'            => 'Registered Assets',
        'available-assets-count'  => 'Available in Warehouse',
        'held-assets-count'       => 'Issued in Custody',
        'terminated-assets-count' => 'Scrapped / Lost',
        'total-cost'              => 'Total Valuation',
        'is-active'               => 'Active',
        'company'                 => 'Company',
        'created-at'              => 'Created At',
        'updated-at'              => 'Updated At',
    ],

    'notification' => [
        'completed' => 'Your product export has completed and :count row(s) exported.',
        'failed'    => ':count row(s) failed to export.',
    ],
];
