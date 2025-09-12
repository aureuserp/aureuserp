<?php

return [
    'notification' => [
        'title' => 'Página actualizada',
        'body'  => 'La página ha sido actualizada exitosamente.',
    ],

    'header-actions' => [
        'draft' => [
            'label' => 'Establecer como Borrador',

            'notification' => [
                'title' => 'Página establecida como borrador',
                'body'  => 'La página ha sido establecida como borrador exitosamente.',
            ],
        ],

        'publish' => [
            'label' => 'Publicar',

            'notification' => [
                'title' => 'Página publicada',
                'body'  => 'La página ha sido publicada exitosamente.',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Página eliminada',
                'body'  => 'La página ha sido eliminada exitosamente.',
            ],
        ],
    ],
];
