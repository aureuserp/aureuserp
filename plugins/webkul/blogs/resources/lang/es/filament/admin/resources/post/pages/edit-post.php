<?php

return [
    'notification' => [
        'title' => 'Publicación actualizada',
        'body'  => 'La publicación se ha actualizado correctamente.',
    ],

    'header-actions' => [
        'draft' => [
            'label' => 'Marcar como borrador',

            'notification' => [
                'title' => 'Publicación marcada como borrador',
                'body'  => 'La publicación se ha marcado como borrador correctamente.',
            ],
        ],

        'publish' => [
            'label' => 'Publicar',

            'notification' => [
                'title' => 'Publicación publicada',
                'body'  => 'La publicación se ha publicado correctamente.',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Publicación eliminada',
                'body'  => 'La publicación se ha eliminado correctamente.',
            ],
        ],
    ],
];
