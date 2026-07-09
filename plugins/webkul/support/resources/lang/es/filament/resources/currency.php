<?php

return [
    'title' => 'Monedas',

    'navigation' => [
        'title' => 'Monedas',
    ],

    'form' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Información de la moneda',

                'fields' => [
                    'name'         => 'Nombre de la moneda',
                    'name-tooltip' => 'Introduzca el nombre oficial de la moneda',
                    'symbol'       => 'Símbolo de la moneda',
                    'full-name'    => 'Nombre completo',
                    'iso-numeric'  => 'Código ISO numérico',
                ],
            ],

            'format-information' => [
                'title' => 'Configuración de formato',

                'fields' => [
                    'decimal-places'        => 'Decimales',
                    'rounding'              => 'Precisión de redondeo',
                    'rounding-helper-text'  => 'Establezca la precisión de redondeo para los cálculos de la moneda',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Estado y configuración',

                'fields' => [
                    'status' => 'Estado',
                ],
            ],

            'rates' => [
                'title'       => 'Tasas de cambio',
                'description' => 'Gestione las tasas de cambio históricas de esta moneda respecto a la moneda base (USD).',

                'fields' => [
                    'name'              => 'Fecha',
                    'unit-per-currency' => 'Unidad por :currency',
                    'currency-per-unit' => ':currency por unidad',
                ],

                'add-rate'   => 'Agregar tasa',
                'item-label' => 'Tasa',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Nombre de la moneda',
            'symbol'         => 'Símbolo',
            'full-name'      => 'Nombre completo',
            'iso-numeric'    => 'Código ISO',
            'decimal-places' => 'Decimales',
            'rounding'       => 'Redondeo',
            'status'         => 'Estado',
            'created-at'     => 'Creado el',
            'updated-at'     => 'Actualizado el',
        ],

        'groups' => [
            'name'           => 'Nombre',
            'status'         => 'Estado',
            'decimal-places' => 'Decimales',
            'creation-date'  => 'Fecha de creación',
            'last-update'    => 'Última actualización',
        ],

        'filters' => [
            'status' => 'Estado',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title'   => 'Moneda eliminada',
                    'body'    => 'La moneda se ha eliminado correctamente.',

                    'success' => [
                        'title' => 'Moneda eliminada',
                        'body'  => 'La moneda se ha eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la moneda',
                        'body'  => 'La moneda no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Monedas eliminadas',
                    'body'  => 'Las monedas se han eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Información de la moneda',

                'entries' => [
                    'name'         => 'Nombre de la moneda',
                    'symbol'       => 'Símbolo de la moneda',
                    'full-name'    => 'Nombre completo',
                    'iso-numeric'  => 'Código ISO numérico',
                ],
            ],

            'format-information' => [
                'title' => 'Configuración de formato',

                'entries' => [
                    'decimal-places' => 'Decimales',
                    'rounding'       => 'Precisión de redondeo',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Estado y configuración',

                'entries' => [
                    'status' => 'Estado',
                ],
            ],

            'rates' => [
                'title'       => 'Tasas de cambio',

                'entries' => [
                    'name'              => 'Fecha',
                    'unit-per-currency' => 'Unidad por :currency',
                    'currency-per-unit' => ':currency por unidad',
                ],
            ],
        ],
    ],
];
