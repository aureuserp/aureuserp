<?php

return [
    'title' => 'Gerenciar fatura',

    'breadcrumb' => 'Gerenciar fatura',

    'navigation' => [
        'title' => 'Gerenciar fatura',
    ],

    'form' => [
        'invoice-policy' => [
            'label' => 'Política de faturamento',
            'label-help' => 'Defina como as faturas são geradas a partir dos pedidos de venda.',
            'options'    => [
                'order' => 'Gerar fatura com base nas quantidades pedidas',
                'delivery' => 'Gerar fatura com base nas quantidades entregues',
            ],
        ],
    ],
];
