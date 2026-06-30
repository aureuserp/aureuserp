<?php

return [
    'title' => 'Pago',

    'navigation' => [
        'title' => 'Pagos',
        'group' => 'Facturas',
    ],

    'global-search' => [
        'partner' => 'Contacto',
        'amount'  => 'Importe',
        'date'    => 'Fecha',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type'          => 'Tipo de pago',
                'memo'                  => 'Nota',
                'date'                  => 'Fecha',
                'amount'                => 'Importe',
                'currency'              => 'Moneda',
                'payment-method'        => 'Método de pago',
                'customer'              => 'Cliente',
                'vendor'                => 'Proveedor',
                'journal'               => 'Diario',
                'customer-bank-account' => 'Cuenta bancaria del cliente',
                'vendor-bank-account'   => 'Cuenta bancaria del proveedor',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Nombre',
            'date'            => 'Fecha',
            'journal'         => 'Diario',
            'payment-method'  => 'Método de pago',
            'partner'         => 'Contacto',
            'amount-currency' => 'Importe (moneda)',
            'amount'          => 'Importe',
            'state'           => 'Estado',
            'company'         => 'Empresa',
            'currency'        => 'Moneda',
            'created-by'      => 'Creado por',
        ],

        'groups' => [
            'name'                             => 'Nombre',
            'company'                          => 'Empresa',
            'journal'                          => 'Diario',
            'partner'                          => 'Contacto',
            'payment-method-line'              => 'Línea de método de pago',
            'payment-method'                   => 'Método de pago',
            'partner-bank-account'             => 'Cuenta bancaria del contacto',
            'created-at'                       => 'Creado el',
            'updated-at'                       => 'Actualizado el',
        ],

        'filters' => [
            'company'                          => 'Empresa',
            'journal'                          => 'Diario',
            'customer-bank-account'            => 'Cuenta bancaria del cliente',
            'payment-method'                   => 'Método de pago',
            'currency'                         => 'Moneda',
            'partner'                          => 'Contacto',
            'payment-method-line'              => 'Línea de método de pago',
            'created-at'                       => 'Creado el',
            'updated-at'                       => 'Actualizado el',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pago eliminado',
                    'body'  => 'El pago se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pagos eliminados',
                    'body'  => 'Los pagos se han eliminado correctamente.',
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'Exportar',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'payment-information' => [
                'title'   => 'Información del pago',
                'entries' => [
                    'state'                 => 'Estado',
                    'vendor'                => 'Proveedor',
                    'customer'              => 'Cliente',
                    'payment-type'          => 'Tipo de pago',
                    'journal'               => 'Diario',
                    'customer-bank-account' => 'Cuenta bancaria del cliente',
                    'vendor-bank-account'   => 'Cuenta bancaria del proveedor',
                    'amount'                => 'Importe',
                    'payment-method'        => 'Método de pago',
                    'date'                  => 'Fecha',
                    'memo'                  => 'Nota',
                ],
            ],
        ],
    ],

];
