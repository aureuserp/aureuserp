<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Erro ao atualizar rastreamento',
                    'body' => 'Você não pode alterar o rastreamento de estoque de um produto que já foi usado.',
                ],

                'track-by-update' => [
                    'title' => 'Erro ao atualizar rastreamento',
                    'body' => 'Você tem produto(s) em estoque sem lote/número de série. É possível atribuir lotes/números de série fazendo um ajuste de estoque.',
                ],
            ],
        ],
    ],
];
