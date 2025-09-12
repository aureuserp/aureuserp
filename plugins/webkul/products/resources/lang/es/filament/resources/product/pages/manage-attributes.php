<?php

return [
    'title' => 'Atributos',

    'form' => [
        'attribute' => 'Atributo',
        'values'    => 'Valores',
    ],

    'table' => [
        'description' => 'Advertencia: añadir o eliminar atributos borrará y recreará las variantes existentes y llevará a la pérdida de sus posibles personalizaciones.',

        'header-actions' => [
            'create' => [
                'label' => 'Añadir Atributo',

                'notification' => [
                    'title' => 'Atributo creado',
                    'body'  => 'El atributo ha sido creado exitosamente.',
                ],
            ],
        ],

        'columns' => [
            'attribute' => 'Atributo',
            'values'    => 'Valores',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Atributo actualizado',
                    'body'  => 'El atributo ha sido actualizado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atributo eliminado',
                    'body'  => 'El atributo ha sido eliminado exitosamente.',
                ],
            ],
        ],
    ],
];
