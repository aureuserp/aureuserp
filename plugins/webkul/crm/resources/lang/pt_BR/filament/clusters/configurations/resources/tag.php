<?php

return [
    'model-label'        => 'Tag',
    'plural-model-label' => 'Tags',

    'navigation' => [
        'title' => 'Tags',
        'group' => 'Funil',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Nome da tag',
            'color' => 'Cor',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nome da tag',
            'color'      => 'Cor',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tag atualizada',
                    'body'  => 'A tag foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tag excluída',
                    'body'  => 'A tag foi excluída com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'Nome da tag',
            'color' => 'Cor',
        ],
    ],
];
