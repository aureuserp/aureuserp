<?php

return [
    'notification' => [
        'title' => 'Categoría actualizada',
        'body'  => 'La categoría ha sido actualizada exitosamente.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Categoría eliminada',
                    'body'  => 'La categoría ha sido eliminada exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar la categoría',
                    'body'  => 'La categoría no puede ser eliminada porque está actualmente en uso.',
                ],
            ],
        ],
    ],

    'save' => [
        'notification' => [
            'error' => [
                'title' => 'La actualización de la categoría falló',
            ],
        ],
    ],
];
