<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Erro ao atualizar rastreamento',
                    'body'  => 'Você não pode alterar o rastreamento de estoque de um produto que já foi usado.',
                ],

                'reordering-rules' => [
                    'title' => 'Erro ao atualizar produto',
                    'body'  => 'Você ainda tem regras de reabastecimento ativas neste produto. Arquive ou exclua-as primeiro.',
                ],

                'reserved' => [
                    'title' => 'Erro ao atualizar rastreamento',
                    'body'  => 'Você não pode alterar o rastreamento de estoque de um produto que está atualmente reservado em uma movimentação de estoque. Se precisar alterar o rastreamento de estoque, você deve primeiro cancelar a reserva da movimentação de estoque.',
                ],

                'qty-not-zero' => [
                    'title' => 'Erro ao atualizar rastreamento',
                    'body'  => 'A quantidade disponível deve ser definida como zero antes de alterar o rastreamento de estoque.',
                ],

                'available-quantity' => [
                    'title' => 'Erro ao atualizar produto',
                    'body'  => 'A quantidade disponível deve ser definida como zero antes que você possa marcar este produto como não armazenável.',
                ],

                'track-by-update' => [
                    'title' => 'Erro ao atualizar rastreamento',
                    'body'  => 'Você tem produto(s) em estoque sem lote/número de série. É possível atribuir lotes/números de série fazendo um ajuste de estoque.',
                ],
            ],
        ],
    ],
];
