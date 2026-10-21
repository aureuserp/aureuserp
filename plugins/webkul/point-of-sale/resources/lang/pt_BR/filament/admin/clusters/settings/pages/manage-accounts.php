<?php

return [
    'title' => 'Contabilidade',

    'form' => [
        'fields' => [
            'receivable-account'      => 'Conta a receber padrão',
            'stock-output-account'    => 'Conta de saída de estoque',
            'balancing-account'       => 'Conta de balanceamento padrão',
            'enable-cogs'             => 'Lançar o custo das mercadorias vendidas',
            'enable-cogs-helper-text' => 'Lança um registro de custo a partir do instantâneo do custo do produto. Ainda não há camada de valoração, portanto a conta de saída de estoque funciona como contrapartida do custo das vendas.',
            'allow-balancing-line'    => 'Permitir linha de balanceamento',
        ],
    ],
];
