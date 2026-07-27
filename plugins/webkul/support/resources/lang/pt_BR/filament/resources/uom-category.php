<?php

return [
    'navigation' => [
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
                    'ratio' => 'Fator',
                    'rounding' => 'Precisão de arredondamento',
                ],

                'validations' => [
                    'missing-reference' => 'Esta categoria deve ter uma unidade de medida de referência.',
                    'multiple-references' => 'Esta categoria deve ter apenas uma unidade de medida de referência.',
                    'ratio-greater-than-zero' => 'O fator de conversão de uma unidade de medida não pode ser zero.',
                    'rounding-greater-than-zero' => 'A precisão de arredondamento deve ser estritamente positiva.',
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
