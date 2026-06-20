<?php

return [
    'notification' => [
        'title' => 'Pedido atualizado',
        'body' => 'O pedido foi atualizado com sucesso.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Confirmar',
        ],

        'close' => [
            'label' => 'Fechar',
        ],

        'cancel' => [
            'label' => 'Cancelar',
        ],

        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Pedido excluído',
                    'body' => 'O pedido foi excluído com sucesso.',
                ],

                'error' => [
                    'title' => 'Não foi possível excluir o pedido',
                    'body' => 'O pedido não pode ser excluído porque está em uso.',
                ],
            ],
        ],
    ],
];
