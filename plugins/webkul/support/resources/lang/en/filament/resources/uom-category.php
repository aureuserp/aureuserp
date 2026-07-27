<?php

return [
    'navigation' => [
        'title' => 'UOM Categories',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name' => 'Name',
                ],
            ],

            'uoms' => [
                'title' => 'Units of Measure',

                'fields' => [
                    'uoms'     => 'Units',
                    'type'     => 'Type',
                    'name'     => 'Unit of Measure',
                    'ratio'    => 'Ratio',
                    'rounding' => 'Rounding Precision',
                ],

                'validations' => [
                    'missing-reference'          => 'This category should have a reference unit of measure.',
                    'multiple-references'        => 'This category should only have one reference unit of measure.',
                    'ratio-greater-than-zero'    => 'The conversion ratio for a unit of measure cannot be zero.',
                    'rounding-greater-than-zero' => 'The rounding precision must be strictly positive.',
                ],

                'actions' => [
                    'add' => 'Add Unit',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'uoms'       => 'UOMs',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'groups' => [
            'created-at' => 'Created At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'UOM Category updated',
                    'body'  => 'The UOM category has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'UOM Category deleted',
                    'body'  => 'The UOM category has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'UOM Categories deleted',
                    'body'  => 'The UOM categories has been deleted successfully.',
                ],
            ],
        ],
    ],
];
