<?php

return [
    'form' => [
        'factor-percent' => 'Percentual do fator',
        'factor-ratio' => 'Proporção do fator',
        'repartition-type' => 'Tipo de repartição',
        'document-type' => 'Tipo de documento',
        'account' => 'Conta',
        'tax' => 'Imposto',
        'tax-closing-entry' => 'Lançamento de fechamento de imposto',
    ],

    'table' => [
        'columns' => [
            'factor-percent' => 'Percentual do fator (%)',
            'account' => 'Conta',
            'tax' => 'Imposto',
            'company' => 'Empresa',
            'repartition-type' => 'Tipo de repartição',
            'document-type' => 'Tipo de documento',
            'tax-closing-entry' => 'Lançamento de fechamento de imposto',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Partição de imposto atualizada',
                    'body' => 'A partição de imposto foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Termo de partição de imposto excluído',
                    'body' => 'O termo de partição de imposto foi excluído com sucesso.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Termo de partição de imposto criado',
                    'body' => 'O termo de partição de imposto foi criado com sucesso.',
                ],
            ],
        ],
    ],
];
