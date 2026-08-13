<?php

return [
    'model-label' => 'Sequência',

    'plural-model-label' => 'Sequências',

    'navigation' => [
        'title' => 'Sequências',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',
                'description' => 'As sequências de diários, armazéns e tipos de operação são criadas automaticamente quando esses registros são criados — edite-as aqui. A criação manual só é necessária para sequências personalizadas baseadas em código.',

                'fields' => [
                    'name'      => 'Nome',
                    'code'      => 'Código',
                    'code-help' => 'Identificador técnico usado pelos documentos, ex.: sales.order. Sequências criadas automaticamente já possuem o código correto.',
                    'company'   => 'Empresa',
                ],
            ],

            'format' => [
                'title' => 'Numeração',

                'fields' => [
                    'prefix'          => 'Prefixo',
                    'prefix-help'     => 'Marcadores: %(year), %(y), %(month), %(day). Exemplo: INV/%(year)/',
                    'suffix'          => 'Sufixo',
                    'padding'         => 'Preenchimento do Número',
                    'next-number'     => 'Próximo Número',
                    'next-number-help' => 'Só pode ser aumentado. Para reiniciar a numeração com segurança, exclua a sequência; ela será recriada a partir do maior número de documento existente.',
                    'step'            => 'Incremento',
                    'reset-frequency' => 'Reiniciar Contador',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Nome',
            'code'            => 'Código',
            'applies-to'      => 'Aplica-se A',
            'company'         => 'Empresa',
            'next-preview'    => 'Próximo Número de Documento',
            'next-number'     => 'Próximo Número',
            'reset-frequency' => 'Reiniciar Contador',
        ],

        'variants' => [
            'refund'         => 'Reembolso',
            'payment'        => 'Pagamento',
            'refund-payment' => 'Reembolso + Pagamento',
        ],

        'filters' => [
            'company' => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Sequência atualizada',
                    'body'  => 'A sequência foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sequência excluída',
                    'body'  => 'A sequência foi excluída com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sequências excluídas',
                    'body'  => 'As sequências foram excluídas com sucesso.',
                ],
            ],
        ],
    ],
];
