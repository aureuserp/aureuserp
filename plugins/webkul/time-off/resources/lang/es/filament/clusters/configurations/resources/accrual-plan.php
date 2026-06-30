<?php

return [
    'title'      => 'Plan de acumulación',
    'navigation' => [
        'title' => 'Plan de acumulación',
    ],

    'form' => [
        'fields' => [
            'name'                    => 'Título',
            'is-based-on-worked-time' => 'Se basa en el tiempo trabajado',
            'accrued-gain-time'       => 'Tiempo de ganancia acumulado',
            'carry-over-time'         => 'Tiempo de traspaso',
            'carry-over-date'         => 'Fecha de traspaso',
            'status'                  => 'Estado',
        ],
    ],

    'table' => [
        'columns' => [
            'name'   => 'Nombre',
            'levels' => 'Niveles',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Plan de acumulación eliminado',
                    'body'  => 'El plan de acumulación se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Plan de acumulación eliminado',
                    'body'  => 'El plan de acumulación se ha eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => 'Información básica',
        ],

        'entries' => [
            'name'                    => 'Nombre',
            'is-based-on-worked-time' => 'Se basa en el tiempo trabajado',
            'accrued-gain-time'       => 'Tiempo de ganancia acumulado',
            'carry-over-time'         => 'Tiempo de traspaso',
            'carry-over-day'          => 'Día de traspaso',
            'carry-over-month'        => 'Mes de traspaso',
        ],
    ],
];
