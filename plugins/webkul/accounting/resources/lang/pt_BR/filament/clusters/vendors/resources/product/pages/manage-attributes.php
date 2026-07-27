<?php

return [
    'title' => 'Atributos',

    'form' => [
        'attribute' => 'Atributo',
        'values' => 'Valores',
    ],

    'table' => [
        'description' => 'Aviso: adicionar ou excluir atributos excluirá e recriará as variantes existentes e causará a perda de possíveis personalizações.',

        'header-actions' => [
            'create' => [
                'label' => 'Adicionar atributo',

                'notification' => [
                    'title' => 'Atributo criado',
                    'body' => 'O atributo foi criado com sucesso.',
                ],
            ],
        ],

        'columns' => [
            'attribute' => 'Atributo',
            'values' => 'Valores',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Atributo atualizado',
                    'body' => 'O atributo foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atributo excluído',
                    'body' => 'O atributo foi excluído com sucesso.',
                ],
            ],
        ],
    ],
];
