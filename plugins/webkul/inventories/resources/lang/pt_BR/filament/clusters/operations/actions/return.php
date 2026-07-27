<?php

return [
    'label' => 'Devolução',

    'modal' => [
        'form' => [
            'columns' => [
                'product' => 'Produto',
                'quantity' => 'Quantidade',
                'uom' => 'Unidade de medida',
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
