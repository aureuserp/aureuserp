<?php

return [
    'navigation' => [
        'title' => 'Regras de armazenamento',
        'group' => 'Gestão de armazéns',
    ],

    'form' => [
        'fields' => [
            'in-location' => 'Quando o produto chega em',
            'product' => 'Produto',
            'product-placeholder' => 'Todos os produtos',
            'category' => 'Categoria de produto',
            'category-placeholder' => 'Todas as categorias',
            'storage-category' => 'Categoria de armazenamento',
            'out-location' => 'Armazenar em',
            'sub-location' => 'Sublocal',
            'company' => 'Empresa',
        ],
    ],

    'table' => [
        'columns' => [
            'in-location' => 'Quando o produto chega em',
            'product' => 'Produto',
            'category' => 'Categoria de produto',
            'storage-category' => 'Categoria de armazenamento',
            'out-location' => 'Armazenar em',
            'sub-location' => 'Sublocal',
            'company' => 'Empresa',
            'deleted-at' => 'Excluído em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Regra de armazenamento atualizada',
                    'body' => 'A regra de armazenamento foi atualizada com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Regra de armazenamento restaurada',
                    'body' => 'A regra de armazenamento foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Regra de armazenamento excluída',
                    'body' => 'A regra de armazenamento foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Não foi possível excluir a regra de armazenamento',
                        'body' => 'A regra de armazenamento não pode ser excluída permanentemente porque é referenciada por outros registros.',
                    ],

                    'success' => [
                        'title' => 'Regra de armazenamento excluída permanentemente',
                        'body' => 'A regra de armazenamento foi excluída permanentemente com sucesso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Regras de armazenamento restauradas',
                    'body' => 'As regras de armazenamento foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Regras de armazenamento excluídas',
                    'body' => 'As regras de armazenamento foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Não foi possível excluir as regras de armazenamento',
                        'body' => 'Algumas regras de armazenamento não podem ser excluídas permanentemente porque são referenciadas por outros registros.',
                    ],

                    'success' => [
                        'title' => 'Regras de armazenamento excluídas permanentemente',
                        'body' => 'As regras de armazenamento foram excluídas permanentemente com sucesso.',
                    ],
                ],
            ],
        ],
    ],
];
