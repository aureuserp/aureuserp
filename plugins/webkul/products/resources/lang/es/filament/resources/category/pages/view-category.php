<?php

return [
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
];
