<?php

return [
    'title' => 'Atributos',

    'form' => [
        'attribute' => 'Atributo',
        'values'    => 'Valores',
    ],

    'table' => [
        'description' => 'Advertencia: agregar o eliminar atributos borrará y recreará las variantes existentes y provocará la pérdida de sus posibles personalizaciones.',

        'header-actions' => [
            'create' => [
                'label' => 'Agregar Atributo',

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
