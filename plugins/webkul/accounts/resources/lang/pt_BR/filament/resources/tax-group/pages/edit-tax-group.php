<?php

return [
    'notification' => [
        'title' => 'Grupo de impostos atualizado',
        'body' => 'O grupo de impostos foi atualizado com sucesso.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Grupo de impostos excluído',
                    'body' => 'O grupo de impostos foi excluído com sucesso.',
                ],

                'error' => [
                    'title' => 'Grupo de impostos não pôde ser excluído',
                    'body' => 'O grupo de impostos não pode ser excluído porque está em uso no momento.',
                ],
            ],
        ],
    ],
];
