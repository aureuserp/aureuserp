<?php

return [
    'title' => 'Pago',

    'navigation' => [
        'title' => 'Pagos',
        'group' => 'Facturas',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type'          => 'Tipo de Pago',
                'memo'                  => 'Memo',
                'date'                  => 'Fecha',
                'amount'                => 'Monto',
                'payment-method'        => 'Método de Pago',
                'customer'              => 'Cliente',
                'journal'               => 'Diario',
                'customer-bank-account' => 'Cuenta Bancaria del Cliente',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                             => 'Nombre',
            'company'                          => 'Empresa',
            'bank-account-holder'              => 'Titular de la Cuenta Bancaria',
            'paired-internal-transfer-payment' => 'Pago de Transferencia Interna Emparejado',
            'payment-method-line'              => 'Línea de Método de Pago',
            'payment-method'                   => 'Método de Pago',
            'currency'                         => 'Moneda',
            'partner'                          => 'Socio',
            'outstanding-amount'               => 'Monto Pendiente',
            'destination-account'              => 'Cuenta de Destino',
            'created-by'                       => 'Creado Por',
            'payment-transaction'              => 'Transacción de Pago',
        ],

        'groups' => [
            'name'                             => 'Nombre',
            'company'                          => 'Empresa',
            'partner'                          => 'Socio',
            'payment-method-line'              => 'Línea de Método de Pago',
            'payment-method'                   => 'Método de Pago',
            'partner-bank-account'             => 'Cuenta Bancaria del Socio',
            'paired-internal-transfer-payment' => 'Pago de Transferencia Interna Emparejado',
            'created-at'                       => 'Creado El',
            'updated-at'                       => 'Actualizado El',
        ],

        'filters' => [
            'company'                          => 'Empresa',
            'customer-bank-account'            => 'Cuenta Bancaria del Cliente',
            'paired-internal-transfer-payment' => 'Pago de Transferencia Interna Emparejado',
            'payment-method'                   => 'Método de Pago',
            'currency'                         => 'Moneda',
            'partner'                          => 'Socio',
            'partner-method-line'              => 'Línea de Método del Socio',
            'created-at'                       => 'Creado El',
            'updated-at'                       => 'Actualizado El',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pago eliminado',
                    'body'  => 'El pago ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pagos eliminados',
                    'body'  => 'Los pagos han sido eliminados exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'payment-information' => [
                'title'   => 'Información de Pago',
                'entries' => [
                    'state'                 => 'Estado',
                    'payment-type'          => 'Tipo de Pago',
                    'journal'               => 'Diario',
                    'customer-bank-account' => 'Cuenta Bancaria del Cliente',
                    'customer'              => 'Cliente',
                ],
            ],

            'payment-details' => [
                'title'   => 'Detalles del Pago',
                'entries' => [
                    'amount' => 'Monto',
                    'date'   => 'Fecha',
                    'memo'   => 'Memo',
                ],
            ],

            'payment-method' => [
                'title'   => 'Método de Pago',
                'entries' => [
                    'payment-method' => 'Método de Pago',
                ],
            ],
        ],
    ],

];
