<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Imposto excluído',
                    'body' => 'O imposto foi excluído com sucesso.',
                ],

                'error' => [
                    'title' => 'Imposto não pôde ser excluído',
                    'body' => 'O imposto não pode ser excluído porque está em uso no momento.',
                ],
            ],
        ],
    ],
];
