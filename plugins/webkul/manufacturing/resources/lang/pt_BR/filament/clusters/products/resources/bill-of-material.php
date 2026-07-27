<?php

return [
    'navigation' => [
        'title' => 'Listas de materiais',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',
                'fields' => [
                    'reference' => 'Referência',
                    'reference-placeholder' => 'ex.: BOM-001',
                    'product' => 'Produto',
                    'product-variant' => 'Variante do produto',
                    'quantity' => 'Quantidade',
                    'uom' => 'Unidade de medida',
                    'operation-type' => 'Tipo de operação',
                    'company' => 'Empresa',
                    'type' => 'Tipo de lista de materiais',
                ],
            ],
            'miscellaneous' => [
                'title' => 'Diversos',
                'fields' => [
                    'kit-information' => 'Informações do kit',
                    'kit-information-content' => 'Uma lista de materiais de kit é usada para agrupar componentes para transferências ou vendas, em vez de ser produzida por meio de uma ordem de produção.',
                    'manufacturing-lead-time' => 'Prazo de produção',
                    'days-to-prepare-manufacturing-order' => 'Dias para preparar a ordem de produção',
                    'days-suffix' => 'dias',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title' => 'Componentes',
                'add-action' => 'Adicionar uma linha',
                'columns'    => [
                    'component' => 'Componente',
                    'apply-on-variants' => 'Aplicar nas variantes',
                    'consumed-in-operation' => 'Consumido na operação',
                    'highlight-consumption' => 'Destacar consumo',
                    'quantity' => 'Quantidade',
                    'uom' => 'Unidade de medida do produto',
                ],
                'validation' => [
                    'component-different-from-product' => 'O componente deve ser diferente do produto que está sendo fabricado.',
                ],
                'create-form' => [
                    'fields' => [
                        'name' => 'Nome',
                        'type' => 'Tipo',
                        'category' => 'Categoria',
                        'company' => 'Empresa',
                        'uom' => 'Unidade de medida',
                        'uom-placeholder' => 'Unidade de medida',
                    ],
                ],
            ],
            'operations' => [
                'title' => 'Operações',
                'add-action' => 'Adicionar uma linha',
                'actions'    => [
                    'edit' => 'Editar operação',
                    'copy-existing' => 'Copiar operações existentes',
                    'copy-existing-fields' => [
                        'operation' => 'Operação',
                    ],
                ],
                'columns'    => [
                    'operation' => 'Operação',
                    'work-center' => 'Centro de trabalho',
                    'time-mode' => 'Cálculo da duração',
                    'time-mode-batch' => 'Calculado no último',
                    'company' => 'Empresa',
                    'apply-on-variants' => 'Aplicar nas variantes',
                    'duration' => 'Duração (minutos)',
                ],
            ],
            'by-products' => [
                'title' => 'Subprodutos',
                'add-action' => 'Adicionar uma linha',
                'columns'    => [
                    'product' => 'Subproduto',
                    'quantity' => 'Quantidade',
                    'uom' => 'Unidade de medida',
                    'operation' => 'Produzido na operação',
                ],
            ],
            'miscellaneous' => [
                'title' => 'Diversos',
                'fields' => [
                    'ready-to-produce' => 'Prontidão para produção',
                    'routing' => 'Roteamento',
                    'consumption' => 'Consumo flexível',
                    'operation-dependencies' => 'Dependências da operação',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference' => 'Referência',
            'product' => 'Produto',
            'quantity' => 'Quantidade',
            'uom' => 'Unidade de medida',
            'type' => 'Tipo de BOM',
            'company' => 'Empresa',
            'deleted-at' => 'Excluído em',
            'updated-at' => 'Atualizado em',
        ],
        'filters' => [
            'product' => 'Produto',
            'type' => 'Tipo de BOM',
            'company' => 'Empresa',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Lista de materiais restaurada',
                    'body' => 'A lista de materiais foi restaurada com sucesso.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Lista de materiais arquivada',
                    'body' => 'A lista de materiais foi arquivada com sucesso.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lista de materiais excluída',
                        'body' => 'A lista de materiais foi excluída permanentemente.',
                    ],
                    'error' => [
                        'title' => 'Não foi possível excluir a lista de materiais',
                        'body' => 'A lista de materiais não pode ser excluída porque está em uso no momento.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Listas de materiais restauradas',
                    'body' => 'As listas de materiais selecionadas foram restauradas com sucesso.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Listas de materiais arquivadas',
                    'body' => 'As listas de materiais selecionadas foram arquivadas com sucesso.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Listas de materiais excluídas',
                        'body' => 'As listas de materiais selecionadas foram excluídas permanentemente.',
                    ],
                    'error' => [
                        'title' => 'Não foi possível excluir as listas de materiais',
                        'body' => 'Uma ou mais listas de materiais selecionadas estão em uso no momento.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',
                'entries' => [
                    'reference' => 'Referência',
                    'product' => 'Produto',
                    'product-variant' => 'Variante do produto',
                    'quantity' => 'Quantidade',
                    'uom' => 'Unidade de medida',
                    'operation-type' => 'Tipo de operação',
                    'company' => 'Empresa',
                    'type' => 'Tipo de lista de materiais',
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
        'tabs' => [
            'components' => [
                'title' => 'Componentes',
                'entries' => [
                    'component' => 'Componente',
                    'operation' => 'Operação',
                    'quantity' => 'Quantidade',
                    'uom' => 'Unidade de medida do produto',
                ],
            ],
            'operations' => [
                'title' => 'Operações',
                'entries' => [
                    'operation' => 'Operação',
                    'work-center' => 'Centro de trabalho',
                    'time-mode' => 'Cálculo da duração',
                    'duration' => 'Duração (minutos)',
                ],
            ],
            'by-products' => [
                'title' => 'Subprodutos',
                'entries' => [
                    'product' => 'Subproduto',
                    'quantity' => 'Quantidade',
                    'uom' => 'Unidade de medida',
                    'operation' => 'Produzido na operação',
                ],
            ],
            'miscellaneous' => [
                'title' => 'Diversos',
                'entries' => [
                    'kit-information' => 'Informações do kit',
                    'kit-information-content' => 'Uma lista de materiais de kit é usada para agrupar componentes para transferências ou vendas, em vez de ser produzida por meio de uma ordem de produção.',
                    'ready-to-produce' => 'Prontidão para produção',
                    'routing' => 'Roteamento',
                    'consumption' => 'Consumo flexível',
                    'operation-dependencies' => 'Dependências da operação',
                    'manufacturing-lead-time' => 'Prazo de produção',
                    'days-to-prepare-manufacturing-order' => 'Dias para preparar a ordem de produção',
                    'days-suffix' => 'dias',
                ],
            ],
        ],
    ],
];
