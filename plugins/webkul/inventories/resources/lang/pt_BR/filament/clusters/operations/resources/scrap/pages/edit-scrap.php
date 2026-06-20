<?php

return [
    'notification' => [
        'title' => 'Descarte atualizado',
        'body' => 'O descarte foi atualizado com sucesso.',
    ],

    'header-actions' => [
        'validate' => [
            'label' => 'Validar',

            'notification' => [
                'warning' => [
                    'title' => 'Estoque insuficiente',
                    'body' => 'O descarte tem estoque insuficiente para validação.',
                ],

                'success' => [
                    'title' => 'Descarte marcado como concluído',
                    'body' => 'O descarte foi marcado como concluído com sucesso.',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Descarte excluído',
                    'body' => 'O descarte foi excluído com sucesso.',
                ],

                'error' => [
                    'title' => 'Não foi possível excluir os descartes',
                    'body' => 'Os descartes não podem ser excluídos porque estão em uso.',
                ],
            ],
        ],
    ],
];
