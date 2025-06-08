<?php

return [
    'notification' => [
        'title' => 'Entrada actualizada',
        'body'  => 'La entrada ha sido actualizada exitosamente.',
    ],

    'header-actions' => [
        'draft' => [
            'label' => 'Establecer como Borrador',

            'notification' => [
                'title' => 'Entrada establecida como borrador',
                'body'  => 'La entrada ha sido establecida como borrador exitosamente.',
            ],
        ],

        'publish' => [
            'label' => 'Publicar',

            'notification' => [
                'title' => 'Entrada publicada',
                'body'  => 'La entrada ha sido publicada exitosamente.',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Entrada eliminada',
                'body'  => 'La entrada ha sido eliminada exitosamente.',
            ],
        ],
    ],
];
