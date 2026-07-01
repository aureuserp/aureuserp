<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Dropship excluído',
                    'body' => 'O dropship foi excluído com sucesso.',
                ],

                'error' => [
                    'title' => 'Não foi possível excluir o dropship',
                    'body' => 'O dropship não pode ser excluído porque está em uso.',
                ],
            ],
        ],
    ],
];
