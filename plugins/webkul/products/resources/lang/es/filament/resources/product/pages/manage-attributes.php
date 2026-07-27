<?php

return [
    'title' => 'Atributos',

    'form' => [
        'attribute' => 'Atributo',
        'values'    => 'Valores',
    ],

    'table' => [
        'description' => 'Advertencia: agregar o eliminar atributos eliminará y recreará las variantes existentes y llevará a la pérdida de sus posibles personalizaciones.',

        'header-actions' => [
            'create' => [
                'label' => 'Agregar atributo',

                'notification' => [
                    'title' => 'Atributo creado',
                    'body'  => 'El atributo ha sido creado correctamente.',
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
                    'body'  => 'El atributo ha sido actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atributo eliminado',
                    'body'  => 'El atributo ha sido eliminado correctamente.',
                ],
            ],
        ],
    ],
];
