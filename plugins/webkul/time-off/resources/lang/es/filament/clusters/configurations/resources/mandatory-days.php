<?php

return [
    'title' => 'Días obligatorios',

    'model-label' => 'Día obligatorio',

    'navigation' => [
        'title' => 'Días festivos obligatorios',
    ],

    'form' => [
        'fields' => [
            'name'       => 'Nombre',
            'start-date' => 'Fecha de inicio',
            'end-date'   => 'Fecha de fin',
            'color'      => 'Color',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nombre',
            'company-name' => 'Nombre de la empresa',
            'created-by'   => 'Creado por',
            'start-date'   => 'Fecha de inicio',
            'end-date'     => 'Fecha de fin',
        ],

        'filters' => [
            'name'         => 'Nombre',
            'company-name' => 'Nombre de la empresa',
            'created-by'   => 'Creado por',
            'start-date'   => 'Fecha de inicio',
            'end-date'     => 'Fecha de fin',
        ],

        'groups' => [
            'name'         => 'Nombre',
            'company-name' => 'Nombre de la empresa',
            'created-by'   => 'Creado por',
            'start-date'   => 'Fecha de inicio',
            'end-date'     => 'Fecha de fin',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Día obligatorio actualizado',
                    'body'  => 'El día obligatorio se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Día obligatorio eliminado',
                    'body'  => 'El día obligatorio se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Días obligatorios eliminados',
                    'body'  => 'Los días obligatorios se han eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'       => 'Nombre',
            'start-date' => 'Fecha de inicio',
            'end-date'   => 'Fecha de fin',
            'color'      => 'Color',
        ],
    ],
];
