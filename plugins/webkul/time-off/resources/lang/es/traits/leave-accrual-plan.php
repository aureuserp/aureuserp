<?php

return [
    'form' => [
        'fields' => [
            'accrual-amount'              => 'Cantidad de acumulación',
            'accrual-value-type'          => 'Tipo de valor de acumulación',
            'accrual-frequency'           => 'Frecuencia de acumulación',
            'accrual-day'                 => 'Día de acumulación',
            'day-of-month'                => 'Día del mes',
            'first-day-of-month'          => 'Primer día del mes',
            'second-day-of-month'         => 'Segundo día del mes',
            'first-period-month'          => 'Mes del primer periodo',
            'first-period-day'            => 'Día del primer periodo',
            'second-period-month'         => 'Mes del segundo periodo',
            'second-period-day'           => 'Día del segundo periodo',
            'first-period-year'           => 'Año del primer periodo',
            'cap-accrued-time'            => 'Limitar tiempo acumulado',
            'days'                        => 'Días',
            'start-count'                 => 'Conteo inicial',
            'start-type'                  => 'Tipo de inicio',
            'action-with-unused-accruals' => 'Acción con acumulaciones no utilizadas',
            'milestone-cap'               => 'Límite de hito',
            'maximum-leave-yearly'        => 'Ausencia máxima anual',
            'accrual-validity'            => 'Validez de la acumulación',
            'accrual-validity-count'      => 'Conteo de validez de la acumulación',
            'accrual-validity-type'       => 'Tipo de validez de la acumulación',
            'advanced-accrual-settings'   => 'Configuración avanzada de acumulación',
            'after-allocation-start'      => 'Después de la fecha de inicio de la asignación',
            'to-date'                     => 'Hasta la fecha',
        ],
    ],

    'table' => [
        'columns' => [
            'accrual-amount'     => 'Cantidad de acumulación',
            'accrual-value-type' => 'Tipo de valor de acumulación',
            'frequency'          => 'Frecuencia',
            'maximum-leave-days' => 'Días máximos de ausencia',
        ],

        'groups' => [
            'accrual-amount'       => 'Cantidad de acumulación',
            'accrual-value-type'   => 'Tipo de valor de acumulación',
            'frequency'            => 'Frecuencia',
            'maximum-leave-days'   => 'Días máximos de ausencia',
        ],

        'filters' => [
            'accrual-frequency'           => 'Frecuencia de acumulación',
            'start-type'                  => 'Tipo de inicio',
            'cap-accrued-time'            => 'Limitar tiempo acumulado',
            'action-with-unused-accruals' => 'Acción con acumulaciones no utilizadas',
            'accrual-amount'              => 'Cantidad de acumulación',
            'accrual-frequency'           => 'Frecuencia de acumulación',
            'start-type'                  => 'Tipo de inicio',
            'created-at'                  => 'Creado el',
            'updated-at'                  => 'Actualizado el',
        ],

        'header-actions' => [
            'created' => [
                'title' => 'Nuevo plan de acumulación de ausencias',

                'notification' => [
                    'title' => 'Plan de acumulación de ausencias creado',
                    'body'  => 'El plan de acumulación de ausencias se ha creado correctamente.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Plan de acumulación de ausencias actualizado',
                    'body'  => 'El plan de acumulación de ausencias se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plan de acumulación de ausencias eliminado',
                    'body'  => 'El plan de acumulación de ausencias se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [

            'delete' => [
                'notification' => [
                    'title' => 'Planes de acumulación de ausencias eliminados',
                    'body'  => 'Los planes de acumulación de ausencias se han eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'accrual-amount'              => 'Cantidad de acumulación',
            'accrual-value-type'          => 'Tipo de valor de acumulación',
            'accrual-frequency'           => 'Frecuencia de acumulación',
            'accrual-day'                 => 'Día de acumulación',
            'day-of-month'                => 'Día del mes',
            'first-day-of-month'          => 'Primer día del mes',
            'second-day-of-month'         => 'Segundo día del mes',
            'first-period-month'          => 'Mes del primer periodo',
            'first-period-day'            => 'Día del primer periodo',
            'second-period-month'         => 'Mes del segundo periodo',
            'second-period-day'           => 'Día del segundo periodo',
            'first-period-year'           => 'Año del primer periodo',
            'cap-accrued-time'            => 'Limitar tiempo acumulado',
            'days'                        => 'Días',
            'start-count'                 => 'Conteo inicial',
            'start-type'                  => 'Tipo de inicio',
            'action-with-unused-accruals' => 'Acción con acumulaciones no utilizadas',
            'milestone-cap'               => 'Límite de hito',
            'maximum-leave-yearly'        => 'Ausencia máxima anual',
            'accrual-validity'            => 'Validez de la acumulación',
            'accrual-validity-count'      => 'Conteo de validez de la acumulación',
            'accrual-validity-type'       => 'Tipo de validez de la acumulación',
            'advanced-accrual-settings'   => 'Configuración avanzada de acumulación',
            'after-allocation-start'      => 'Después de la fecha de inicio de la asignación',
        ],
    ],
];
