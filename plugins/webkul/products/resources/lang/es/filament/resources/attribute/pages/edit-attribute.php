<?php

return [
    'notification' => [
        'title' => 'Atributo actualizado',
        'body'  => 'El atributo ha sido actualizado correctamente.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Atributo eliminado',
                    'body'  => 'El atributo ha sido eliminado correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el atributo',
                    'body'  => 'El atributo no puede eliminarse porque está en uso por el producto: :products',
                ],
            ],
        ],
    ],
];
