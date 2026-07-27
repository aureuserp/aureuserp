<?php

return [
    'title' => 'Tag',

    'navigation' => [
        'title' => 'Tag',
        'group' => 'Pedidos de venda',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nome',
            'color' => 'Cor',
        ],
    ],

    'table' => [
        'columns' => [
            'created-by' => 'Criado por',
            'name' => 'Nome',
            'color' => 'Cor',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tag do produto atualizada',
                    'body' => 'A tag do produto foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tag do produto excluída',
                    'body' => 'A tag do produto foi excluída com sucesso.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tag do produto excluída',
                    'body' => 'A tag do produto foi excluída com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'color' => 'Cor',
        ],
    ],
];
