<?php

return [
    'form' => [
        'partner' => 'Partner',
        'name'    => 'Nombre',
        'email'   => 'Email',
        'phone'   => 'Teléfono',
        'mobile'  => 'Móvil',
        'type'    => 'Tipo',
        'address' => 'Dirección',
        'city'    => 'Ciudad',
        'street1' => 'Calle 1',
        'street2' => 'Calle 2',
        'state'   => 'Estado',
        'zip'     => 'Código Postal',
        'code'    => 'Código',
        'country' => 'País',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Agregar Dirección',

                'notification' => [
                    'title' => 'Dirección creada',
                    'body'  => 'La dirección ha sido creada exitosamente.',
                ],
            ],
        ],

        'columns' => [
            'type'    => 'Tipo',
            'name'    => 'Nombre de Contacto',
            'address' => 'Dirección',
            'city'    => 'Ciudad',
            'street1' => 'Calle 1',
            'street2' => 'Calle 2',
            'state'   => 'Estado',
            'zip'     => 'Código Postal',
            'country' => 'País',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Dirección actualizada',
                    'body'  => 'La dirección ha sido actualizada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Dirección eliminada',
                    'body'  => 'La dirección ha sido eliminada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Direcciones eliminadas',
                    'body'  => 'Las direcciones han sido eliminadas exitosamente.',
                ],
            ],
        ],
    ],
];
