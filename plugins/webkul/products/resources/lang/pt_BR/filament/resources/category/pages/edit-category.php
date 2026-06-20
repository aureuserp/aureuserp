<?php

return [
    'notification' => [
        'title' => 'Categoria atualizada',
        'body' => 'A categoria foi atualizada com sucesso.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Categoria excluída',
                    'body' => 'A categoria foi excluída com sucesso.',
                ],

                'error' => [
                    'title' => 'Categoria não pôde ser excluída',
                    'body' => 'A categoria não pode ser excluída porque está em uso no momento.',
                ],
            ],
        ],
    ],

    'save' => [
        'notification' => [
            'error' => [
                'title' => 'Falha ao atualizar categoria',
            ],
        ],
    ],
];
