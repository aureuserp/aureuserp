<?php

return [
    'notification' => [
        'title' => 'Post atualizado',
        'body' => 'O post foi atualizado com sucesso.',
    ],

    'header-actions' => [
        'draft' => [
            'label' => 'Definir como rascunho',

            'notification' => [
                'title' => 'Post definido como rascunho',
                'body' => 'O post foi definido como rascunho com sucesso.',
            ],
        ],

        'publish' => [
            'label' => 'Publicar',

            'notification' => [
                'title' => 'Post publicado',
                'body' => 'O post foi publicado com sucesso.',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Post excluído',
                'body' => 'O post foi excluído com sucesso.',
            ],
        ],
    ],
];
