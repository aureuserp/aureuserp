<?php

return [
    'title' => 'Días festivos',

    'model-label' => 'Día festivo',

    'navigation' => [
        'title' => 'Días festivos',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nombre',
            'name-placeholder' => 'Introduzca el nombre del día festivo',
            'date-from'        => 'Fecha de inicio',
            'date-to'          => 'Fecha de fin',
            'color'            => 'Color',
            'calendar'         => 'Calendario',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nombre',
            'company-name' => 'Nombre de la empresa',
            'calendar'     => 'Calendario',
            'created-by'   => 'Creado por',
            'date-from'    => 'Fecha de inicio',
            'date-to'      => 'Fecha de fin',
        ],

        'filters' => [
            'name'         => 'Nombre',
            'company-name' => 'Nombre de la empresa',
            'created-by'   => 'Creado por',
            'date-from'    => 'Fecha de inicio',
            'date-to'      => 'Fecha de fin',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'groups' => [
            'name'         => 'Nombre',
            'company-name' => 'Nombre de la empresa',
            'created-by'   => 'Creado por',
            'date-from'    => 'Fecha de inicio',
            'date-to'      => 'Fecha de fin',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Día festivo actualizado',
                    'body'  => 'El día festivo se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Día festivo eliminado',
                    'body'  => 'El día festivo se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Días festivos eliminados',
                    'body'  => 'Los días festivos se han eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'      => 'Nombre',
            'date-from' => 'Fecha de inicio',
            'date-to'   => 'Fecha de fin',
            'color'     => 'Color',
        ],
    ],
];
