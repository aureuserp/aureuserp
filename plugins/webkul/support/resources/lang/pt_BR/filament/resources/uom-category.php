<?php

return [
    'navigation' => [
        'group' => 'Configurações',
        'title' => 'Categorias de UOM',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name' => 'Nome',
                ],
            ],

            'uoms' => [
                'title' => 'Unidades de medida',

                'fields' => [
                    'uoms' => 'Unidades',
                    'type' => 'Tipo',
                    'name' => 'Nome',
                    'factor' => 'Fator',
                    'rounding' => 'Precisão de arredondamento',
                ],

                'actions' => [
                    'add' => 'Adicionar unidade',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'uoms' => 'UOMs',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'created-at' => 'Criado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Categoria de UOM atualizada',
                    'body' => 'A categoria de UOM foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Categoria de UOM excluída',
                    'body' => 'A categoria de UOM foi excluída com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categorias de UOM excluídas',
                    'body' => 'As categorias de UOM foram excluídas com sucesso.',
                ],
            ],
        ],
    ],
];
