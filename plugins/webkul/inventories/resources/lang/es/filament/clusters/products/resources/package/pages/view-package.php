<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',

            'actions' => [
                'without-content' => [
                    'label' => 'Imprimir Código de Barras',
                ],

                'with-content' => [
                    'label' => 'Imprimir Código de Barras con Contenido',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Paquete Eliminado',
                    'body'  => 'El paquete ha sido eliminado exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el paquete',
                    'body'  => 'El paquete no se puede eliminar porque está actualmente en uso.',
                ],
            ],
        ],
    ],
];
