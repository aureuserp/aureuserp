<?php

return [
    'notification' => [
        'title' => 'Acordo de compra atualizado',
        'body' => 'O acordo de compra foi atualizado com sucesso.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Confirmar',

            'notification' => [
                'unable' => [
                    'title' => 'Não foi possível confirmar o acordo de compra',
                    'body' => 'Adicione pelo menos uma linha de produto antes de confirmar este acordo de compra.',
                ],
            ],
        ],

        'close' => [
            'label' => 'Fechar',
            'notification' => [
                'warning' => [
                    'title' => 'Não foi possível fechar o acordo de compra',
                    'body' => 'Você não pode fechar este acordo de compra porque algumas solicitações de cotação relacionadas não estão com status Concluído ou Cancelado.',
                ],
            ],
        ],

        'cancel' => [
            'label' => 'Cancelar',
        ],

        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'title' => 'Acordo de compra excluído',
                'body' => 'O acordo de compra foi excluído com sucesso.',
            ],
        ],
    ],
];
