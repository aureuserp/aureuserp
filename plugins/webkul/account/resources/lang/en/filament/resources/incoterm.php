<?php

return [
    'form' => [
        'fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Code',
            'name'       => 'Name',
            'created-by' => 'Created By',
        ],

        'groups' => [
            'code' => 'Code',
            'name' => 'Name',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Incoterm updated',
                    'body'  => 'The incoterm has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterm deleted',
                    'body'  => 'The incoterm has been deleted successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Incoterm restored',
                    'body'  => 'The incoterm has been restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Incoterms restored',
                    'body'  => 'The incoterms has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterms deleted',
                    'body'  => 'The incoterms has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Incoterms force deleted',
                    'body'  => 'The incoterms has been force deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Name',
            'code' => 'Code',
        ],
    ],
];
