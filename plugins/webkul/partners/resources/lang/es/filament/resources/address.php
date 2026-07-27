<?php

return [
    'form' => [
        'partner' => 'Contacto',
        'name'    => 'Nombre',
        'email'   => 'Correo electrónico',
        'phone'   => 'Teléfono',
        'mobile'  => 'Móvil',
        'type'    => 'Tipo',
        'address' => 'Dirección',
        'city'    => 'Ciudad',
        'street1' => 'Calle 1',
        'street2' => 'Calle 2',
        'state'   => 'Provincia',
        'zip'     => 'Código postal',
        'code'    => 'Código',
        'country' => 'País',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Añadir dirección',

                'notification' => [
                    'title' => 'Dirección creada',
                    'body'  => 'La dirección se ha creado correctamente.',
                ],
            ],
        ],

        'columns' => [
            'type'    => 'Tipo',
            'name'    => 'Nombre de contacto',
            'address' => 'Dirección',
            'city'    => 'Ciudad',
            'street1' => 'Calle 1',
            'street2' => 'Calle 2',
            'state'   => 'Provincia',
            'zip'     => 'Código postal',
            'country' => 'País',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Dirección actualizada',
                    'body'  => 'La dirección se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Dirección eliminada',
                    'body'  => 'La dirección se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Direcciones eliminadas',
                    'body'  => 'Las direcciones se han eliminado correctamente.',
                ],
            ],
        ],
    ],
];
