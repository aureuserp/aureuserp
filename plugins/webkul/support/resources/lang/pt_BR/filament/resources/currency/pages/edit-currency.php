<?php

return [
    'title' => 'Editar moeda',

    'notification' => [
        'title' => 'Moeda atualizada',
        'body'  => 'A moeda foi atualizada com sucesso.',
    ],

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
