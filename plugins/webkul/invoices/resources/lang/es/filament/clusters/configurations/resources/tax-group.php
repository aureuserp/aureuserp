<?php

return [
    'title' => 'Grupos de Impuestos',

    'navigation' => [
        'title' => 'Grupos de Impuestos',
        'group' => 'Contabilidad',
    ],

    'global-search' => [
        'company-name' => 'Nombre de la Empresa',
        'payment-term' => 'Condición de Pago',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'company'            => 'Empresa',
                'country'            => 'País',
                'name'               => 'Nombre',
                'preceding-subtotal' => 'Subtotal Precedente',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company'            => 'Empresa',
            'country'            => 'País',
            'created-by'         => 'Creado Por',
            'name'               => 'Nombre',
            'preceding-subtotal' => 'Subtotal Precedente',
            'created-at'         => 'Creado El',
            'updated-at'         => 'Actualizado El',
        ],

        'groups' => [
            'name'               => 'Nombre',
            'company'            => 'Empresa',
            'country'            => 'País',
            'created-by'         => 'Creado Por',
            'created-at'         => 'Creado El',
            'updated-at'         => 'Actualizado El',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Condición de pago eliminada',
                    'body'  => 'La condición de pago ha sido eliminada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Grupos de Impuestos eliminados',
                    'body'  => 'Los Grupos de Impuestos han sido eliminados exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'company'            => 'Empresa',
                'country'            => 'País',
                'name'               => 'Nombre',
                'preceding-subtotal' => 'Subtotal Precedente',
            ],
        ],
    ],
];
