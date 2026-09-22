<?php

return [
    'navigation' => [
        'title' => 'Listas de preços',
        'group' => 'Produtos',
    ],

    'header-actions' => [
        'create' => [
            'label' => 'Nova lista de preços',
        ],
    ],

    'form' => [
        'section' => [
            'general' => [
                'title' => 'Informações gerais',

                'fields' => [
                    'name'     => 'Nome da lista de preços',
                    'currency' => 'Moeda',
                    'company'  => 'Empresa',
                    'status'   => 'Ativo',
                ],
            ],

            'rules' => [
                'title'        => 'Regras de preço',
                'description'  => 'A primeira regra que corresponde a um produto decide seu preço. Regras mais específicas prevalecem sobre as mais amplas.',
                'add-rule'     => 'Adicionar regra',
                'all-products' => 'Todos os produtos',

                'actions' => [
                    'edit' => 'Editar regra',
                ],

                'columns' => [
                    'apply-to'     => 'Aplicar em',
                    'target'       => 'Aplica-se a',
                    'min-quantity' => 'Qtd. mínima',
                    'type'         => 'Calcular preço',
                    'price'        => 'Preço',
                    'period'       => 'Período',
                ],

                'formula' => [
                    'discount'     => 'desconto',
                    'markup'       => 'acréscimo',
                    'rule-tip'     => ':base com :discount % de :type e :surcharge de taxa extra'."\n".'Exemplo: :amount * :factor + :surcharge → :total',
                    'rounding-tip' => 'Dica: quer arredondar para 9,99? Arredonde para 10,00 e defina uma taxa extra de -0,01.',
                ],

                'fields' => [
                    'apply-to-product'      => 'Produto',
                    'apply-to-category'     => 'Categoria',
                    'all-products'          => 'Todos os produtos',
                    'all-categories'        => 'Todas as categorias',
                    'all-variants'          => 'Todas as variantes',
                    'variant'               => 'Variante',
                    'on'                    => 'sobre',
                    'sales-price'           => 'Preço de venda',
                    'apply-to'              => 'Aplicar a',
                    'product'               => 'Produto',
                    'category'              => 'Categoria do produto',
                    'min-quantity'          => 'Qtd. mín.',
                    'type'                  => 'Tipo de preço',
                    'fixed-price'           => 'Preço fixo',
                    'percent-price'         => 'Desconto',
                    'percent-price-helper'  => 'Use um valor negativo para aplicar um acréscimo.',
                    'base'                  => 'Preço base',
                    'base-price-list'       => 'Outra lista de preços',
                    'price-discount'        => 'Desconto',
                    'price-discount-helper' => 'Use um valor negativo para aplicar um acréscimo.',
                    'price-markup'          => 'Acréscimo',
                    'price-round'           => 'Arredondar para',
                    'price-round-helper'    => 'Arredonda o preço para um múltiplo deste valor, após o desconto e antes da taxa extra.',
                    'price-surcharge'       => 'Taxa extra',
                    'price-min-margin'      => 'Margem mín.',
                    'price-max-margin'      => 'Margem máx.',
                    'starts-at'             => 'Período de validade',
                    'ends-at'               => 'Data final',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'     => 'Lista de preços',
            'currency' => 'Moeda',
            'company'  => 'Empresa',
            'rules'    => 'Regras',
            'status'   => 'Ativo',
        ],

        'filters' => [
            'status'   => 'Ativo',
            'currency' => 'Moeda',
        ],
    ],
];
