<?php

return [
    'notification' => [
        'title' => 'Produto atualizado',
        'body' => 'O produto foi atualizado com sucesso.',
    ],

    'header-actions' => [
        'update-quantity' => [
            'label' => 'Atualizar quantidade',
            'modal-heading' => 'Atualizar quantidade do produto',
            'modal-submit-action-label' => 'Atualizar',

            'form' => [
                'fields' => [
                    'on-hand-qty' => 'Quantidade em estoque',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Produto excluído',
                'body' => 'O produto foi excluído com sucesso.',
            ],
        ],
    ],
];
