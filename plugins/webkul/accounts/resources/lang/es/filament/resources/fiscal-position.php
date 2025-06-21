<?php

return [
    'global-search' => [
        'zip-from' => 'Código Postal Desde',
        'zip-to'   => 'Código Postal Hasta',
        'name'     => 'Nombre',
    ],

    'form' => [
        'fields' => [
            'name'                 => 'Nombre',
            'foreign-vat'          => 'IVA Extranjero',
            'country'              => 'País',
            'country-group'        => 'Grupo de Países',
            'zip-from'             => 'Código Postal Desde',
            'zip-to'               => 'Código Postal Hasta',
            'detect-automatically' => 'Detectar Automáticamente',
            'notes'                => 'Notas',
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Nombre',
            'company'              => 'Empresa',
            'country'              => 'País',
            'country-group'        => 'Grupo de Países',
            'created-by'           => 'Creado Por',
            'zip-from'             => 'Código Postal Desde',
            'zip-to'               => 'Código Postal Hasta',
            'status'               => 'Estado',
            'detect-automatically' => 'Detectar Automáticamente',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Posición fiscal eliminada',
                    'body'  => 'La posición fiscal ha sido eliminada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Posiciones fiscales eliminadas',
                    'body'  => 'Las posiciones fiscales han sido eliminadas exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'                 => 'Nombre',
            'foreign-vat'          => 'IVA Extranjero',
            'country'              => 'País',
            'country-group'        => 'Grupo de Países',
            'zip-from'             => 'Código Postal Desde',
            'zip-to'               => 'Código Postal Hasta',
            'detect-automatically' => 'Detectar Automáticamente',
            'notes'                => 'Notas',
        ],
    ],
];
