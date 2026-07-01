<?php

return [
    'assets' => [
        'label' => 'Ativos',
        'options' => [
            'receivable' => 'A receber',
            'cash' => 'Banco e caixa',
            'current' => 'Ativos circulantes',
            'non-current' => 'Ativos não circulantes',
            'prepayments' => 'Pagamentos antecipados',
            'fixed' => 'Ativos imobilizados',
        ],
    ],

    'liabilities' => [
        'label' => 'Passivos',
        'options' => [
            'payable' => 'A pagar',
            'credit-card' => 'Cartão de crédito',
            'current' => 'Passivos circulantes',
            'non-current' => 'Passivos não circulantes',
        ],
    ],

    'equity' => [
        'label' => 'Patrimônio líquido',
        'options' => [
            'equity' => 'Patrimônio líquido',
            'unaffected' => 'Resultado do ano corrente',
        ],
    ],

    'income' => [
        'label' => 'Receitas',
        'options' => [
            'income' => 'Receitas',
            'other' => 'Outras receitas',
        ],
    ],

    'expenses' => [
        'label' => 'Despesas',
        'options' => [
            'expense' => 'Despesas',
            'depreciation' => 'Depreciação',
            'direct-cost' => 'Custo da receita',
        ],
    ],

    'off-balance' => [
        'label' => 'Fora do balanço',
        'options' => [
            'off-balance' => 'Fora do balanço',
        ],
    ],
];
