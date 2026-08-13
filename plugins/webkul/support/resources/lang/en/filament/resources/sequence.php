<?php

return [
    'model-label' => 'Sequence',

    'plural-model-label' => 'Sequences',

    'navigation' => [
        'title' => 'Sequences',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',
                'description' => 'Sequences for journals, warehouses and operation types are created automatically when those records are created — edit them here. Manual creation is only needed for custom code-based sequences.',

                'fields' => [
                    'name'      => 'Name',
                    'code'      => 'Code',
                    'code-help' => 'Technical identifier used by documents, e.g. sales.order. Auto-created sequences already have the right code.',
                    'company'   => 'Company',
                ],
            ],

            'format' => [
                'title' => 'Numbering',

                'fields' => [
                    'prefix'          => 'Prefix',
                    'prefix-help'     => 'Placeholders: %(year), %(y), %(month), %(day). Example: INV/%(year)/',
                    'suffix'          => 'Suffix',
                    'padding'         => 'Number Padding',
                    'next-number'     => 'Next Number',
                    'next-number-help' => 'Can only be increased. To restart numbering safely, delete the sequence — it will be recreated from the highest existing document number.',
                    'step'            => 'Step',
                    'reset-frequency' => 'Reset Counter',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Name',
            'code'            => 'Code',
            'applies-to'      => 'Applies To',
            'company'         => 'Company',
            'next-preview'    => 'Next Document Number',
            'next-number'     => 'Next Number',
            'reset-frequency' => 'Reset Counter',
        ],

        'variants' => [
            'refund'         => 'Refund',
            'payment'        => 'Payment',
            'refund-payment' => 'Refund + Payment',
        ],

        'filters' => [
            'company' => 'Company',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Sequence updated',
                    'body'  => 'The sequence has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sequence deleted',
                    'body'  => 'The sequence has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sequences deleted',
                    'body'  => 'The sequences have been deleted successfully.',
                ],
            ],
        ],
    ],
];
