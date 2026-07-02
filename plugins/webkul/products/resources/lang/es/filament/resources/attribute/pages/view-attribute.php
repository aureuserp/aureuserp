<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Atributo eliminado',
                    'body'  => 'El atributo ha sido eliminado correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el atributo',
                    'body'  => 'El atributo no puede eliminarse porque está en uso.',
                ],
            ],
        ],
    ],
];
