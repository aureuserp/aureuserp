<?php

return [
    'notification' => [
        'title' => 'Página actualizada',
        'body'  => 'La página se ha actualizado correctamente.',
    ],

    'header-actions' => [
        'draft' => [
            'label' => 'Marcar como borrador',

            'notification' => [
                'title' => 'Página marcada como borrador',
                'body'  => 'La página se ha marcado como borrador correctamente.',
            ],
        ],

        'publish' => [
            'label' => 'Publicar',

            'notification' => [
                'title' => 'Página publicada',
                'body'  => 'La página se ha publicado correctamente.',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Página eliminada',
                'body'  => 'La página se ha eliminado correctamente.',
            ],
        ],
    ],
];
