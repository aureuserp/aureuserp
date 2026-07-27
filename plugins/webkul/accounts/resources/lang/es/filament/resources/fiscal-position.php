<?php

return [
    'global-search' => [
        'zip-from' => 'Código postal desde',
        'zip-to'   => 'Código postal hasta',
        'name'     => 'Nombre',
    ],

    'form' => [
        'fields' => [
            'name'                   => 'Nombre',
            'foreign-vat'            => 'NIF extranjero',
            'country'                => 'País',
            'country-group'          => 'Grupo de países',
            'zip-from'               => 'Código postal desde',
            'zip-to'                 => 'Código postal hasta',
            'detect-automatically'   => 'Detectar automáticamente',
            'notes'                  => 'Notas',
            'company'                => 'Empresa',
        ],
        'tabs' => [
            'account-mapping' => [
                'table' => [
                    'columns' => [
                        'source-account'      => 'Cuenta de origen',
                        'destination-account' => 'Cuenta de destino',
                    ],
                ],

            ],
            'tax-mapping' => [
                'table' => [
                    'columns' => [
                        'tax-source'      => 'Impuesto de origen',
                        'tax-destination' => 'Impuesto de destino',
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Nombre',
            'company'              => 'Empresa',
            'country'              => 'País',
            'country-group'        => 'Grupo de países',
            'created-by'           => 'Creado por',
            'zip-from'             => 'Código postal desde',
            'zip-to'               => 'Código postal hasta',
            'status'               => 'Estado',
            'detect-automatically' => 'Detectar automáticamente',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Condición de pago eliminada',
                    'body'  => 'La condición de pago se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Posición fiscal eliminada',
                    'body'  => 'La posición fiscal se ha eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'                 => 'Nombre',
            'foreign-vat'          => 'NIF extranjero',
            'country'              => 'País',
            'country-group'        => 'Grupo de países',
            'zip-from'             => 'Código postal desde',
            'zip-to'               => 'Código postal hasta',
            'detect-automatically' => 'Detectar automáticamente',
            'notes'                => 'Notas',
        ],
    ],
];
