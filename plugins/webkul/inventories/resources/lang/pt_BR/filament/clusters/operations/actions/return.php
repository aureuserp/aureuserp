<?php

return [
    'label' => 'Devolução',

    'modal' => [
        'form' => [
            'columns' => [
                'product'                 => 'Produto',
                'quantity'                => 'Quantidade',
                'uom'                     => 'Unidade de medida',
                'excess-quantity-tooltip' => 'A quantidade a devolver é maior que a quantidade processada na operação original.',
            ],
        ],
    ],

    'notification' => [
        'no-products' => [
            'body' => 'Nenhum produto para devolver (somente linhas no estado Concluído e ainda não totalmente devolvidas podem ser devolvidas).',
        ],
        'no-quantities' => [
            'body' => 'Informe pelo menos uma quantidade diferente de zero.',
        ],
    ],
];
