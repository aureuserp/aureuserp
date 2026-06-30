<?php

return [
    'notification' => [
        'title' => 'Paquete actualizado',
        'body'  => 'El paquete ha sido actualizado correctamente.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',

            'actions' => [
                'without-content' => [
                    'label' => 'Imprimir código de barras',
                ],

                'with-content' => [
                    'label' => 'Imprimir código de barras con contenido',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Paquete eliminado',
                    'body'  => 'El paquete ha sido eliminado correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el paquete',
                    'body'  => 'El paquete no puede eliminarse porque está en uso actualmente.',
                ],
            ],
        ],
    ],
];
