<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Entrega excluída',
                    'body' => 'A entrega foi excluída com sucesso.',
                ],

                'error' => [
                    'title' => 'Não foi possível excluir a entrega',
                    'body' => 'A entrega não pode ser excluída porque está em uso.',
                ],
            ],
        ],
    ],
];
