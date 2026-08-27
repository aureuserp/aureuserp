<?php

return [
    'title' => 'Visualizar moeda',

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Moeda excluída',
                'body'  => 'A moeda foi excluída com sucesso.',

                'error' => [
                    'title' => 'Não foi possível excluir a moeda',
                    'body'  => 'A moeda não pode ser excluída porque está em uso.',
                ],
            ],
        ],
    ],
];
