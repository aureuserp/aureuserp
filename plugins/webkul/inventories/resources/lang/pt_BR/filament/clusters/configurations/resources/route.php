<?php

return [
    'navigation' => [
        'title' => 'Rotas',
        'group' => 'Gestão de armazéns',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'route' => 'Rota',
                    'route-placeholder' => 'ex.: Recebimento em duas etapas',
                    'company' => 'Empresa',
                ],
            ],

            'applicable-on' => [
                'title' => 'Aplicável a',
                'description' => 'Escolha os locais onde esta rota pode ser aplicada.',

                'fields' => [
                    'products' => 'Produtos',
                    'products-hint-tooltip' => 'Se selecionado, esta rota ficará disponível para seleção no produto.',
                    'product-categories' => 'Categorias de produto',
                    'product-categories-hint-tooltip' => 'Se selecionado, esta rota ficará disponível para seleção na categoria do produto.',
                    'warehouses' => 'Armazéns',
                    'warehouses-hint-tooltip' => 'Quando um armazém é atribuído a esta rota, ela será considerada a rota padrão para produtos que passam por esse armazém.',
                    'packaging' => 'Embalagem',
                    'packaging-hint-tooltip' => 'Se selecionado, esta rota ficará disponível para seleção na embalagem.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'route' => 'Rota',
            'company' => 'Empresa',
            'deleted-at' => 'Excluído em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'company' => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Rota atualizada',
                    'body' => 'A rota foi atualizada com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Rota restaurada',
                    'body' => 'A rota foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rota excluída',
                    'body' => 'A rota foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rota excluída permanentemente',
                        'body' => 'A rota foi excluída permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir a rota',
                        'body' => 'A rota não pode ser excluída porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Rotas restauradas',
                    'body' => 'As rotas foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rotas excluídas',
                    'body' => 'As rotas foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rotas excluídas permanentemente',
                        'body' => 'As rotas foram excluídas permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir as rotas',
                        'body' => 'As rotas não podem ser excluídas porque estão em uso no momento.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'entries' => [
                    'route' => 'Rota',
                    'route-placeholder' => 'ex.: Recebimento em duas etapas',
                    'company' => 'Empresa',
                ],
            ],

            'applicable-on' => [
                'title' => 'Aplicável a',
                'description' => 'Selecione os locais onde esta rota pode ser selecionada.',

                'entries' => [
                    'products' => 'Produtos',
                    'products-hint-tooltip' => 'Se selecionado, esta rota ficará disponível para seleção no produto.',
                    'product-categories' => 'Categorias de produto',
                    'product-categories-hint-tooltip' => 'Se selecionado, esta rota ficará disponível para seleção na categoria do produto.',
                    'warehouses' => 'Armazéns',
                    'warehouses-hint-tooltip' => 'Quando um armazém é atribuído a esta rota, ela será considerada a rota padrão para produtos que passam por esse armazém.',
                    'packaging' => 'Embalagem',
                    'packaging-hint-tooltip' => 'Se selecionado, esta rota ficará disponível para seleção na embalagem.',
                ],
            ],

            'record-information' => [
                'title' => 'Informações do registro',

                'entries' => [
                    'created-by' => 'Criado por',
                    'created-at' => 'Criado em',
                    'last-updated' => 'Última atualização',
                ],
            ],
        ],
    ],
];
