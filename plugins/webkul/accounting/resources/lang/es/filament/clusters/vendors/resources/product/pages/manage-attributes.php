<?php

return [
    'title' => 'Atributos',

    'form' => [
        'attribute' => 'Atributo',
        'values'    => 'Valores',
    ],

    'table' => [
        'description' => 'Advertencia: agregar o eliminar atributos eliminará y recreará las variantes existentes y provocará la pérdida de sus posibles personalizaciones.',

        'header-actions' => [
            'create' => [
                'label' => 'Agregar atributo',

                'notification' => [
                    'title' => 'Atributo creado',
                    'body'  => 'El atributo se ha creado correctamente.',
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
                    'body'  => 'El atributo se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atributo eliminado',
                    'body'  => 'El atributo se ha eliminado correctamente.',
                ],
            ],
        ],
    ],
];
