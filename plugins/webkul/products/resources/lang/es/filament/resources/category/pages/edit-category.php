<?php

return [
    'notification' => [
        'title' => 'Categoría actualizada',
        'body'  => 'La categoría ha sido actualizada correctamente.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Categoría eliminada',
                    'body'  => 'La categoría ha sido eliminada correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar la categoría',
                    'body'  => 'La categoría no se puede eliminar porque está en uso.',
                ],
            ],
        ],
    ],

    'save' => [
        'notification' => [
            'error' => [
                'title' => 'Error al actualizar la categoría',
            ],
        ],
    ],
];
