<?php

return [
    'title' => 'Capacidade por embalagens',

    'form' => [
        'package-type' => 'Tipo de embalagem',
        'qty' => 'Quantidade',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Adicionar capacidade por tipo de embalagem',

                'notification' => [
                    'title' => 'Capacidade por tipo de embalagem criada',
                    'body' => 'A capacidade por tipo de embalagem foi adicionada com sucesso.',
                ],
            ],
        ],

        'columns' => [
            'package-type' => 'Tipo de embalagem',
            'qty' => 'Quantidade',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Capacidade por tipo de embalagem atualizada',
                    'body' => 'A capacidade por tipo de embalagem foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Capacidade por tipo de embalagem excluída',
                    'body' => 'A capacidade por tipo de embalagem foi excluída com sucesso.',
                ],
            ],
        ],
    ],
];
