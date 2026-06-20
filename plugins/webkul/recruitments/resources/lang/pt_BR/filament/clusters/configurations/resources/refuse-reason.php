<?php

return [
    'title' => 'Motivo da recusa',

    'navigation' => [
        'title' => 'Motivos de recusa',
        'group' => 'Candidaturas',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nome',
            'template'         => [
                'title' => 'Modelo',
                'applicant-refuse' => 'Recusa do candidato',
                'applicant-not-interested' => 'Candidato não interessado',
            ],
            'name-placeholder' => 'Informe o nome do motivo de recusa',
        ],
    ],

    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'template' => 'Modelo',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'name' => 'Nome',
            'employee' => 'Funcionário',
            'created-by' => 'Criado por',
            'updated-at' => 'Atualizado em',
            'created-at' => 'Criado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Motivo de recusa atualizado',
                    'body' => 'O motivo de recusa foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Motivo de recusa excluído',
                    'body' => 'O motivo de recusa foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Motivos de recusa excluídos',
                    'body' => 'Os motivos de recusa foram excluídos com sucesso.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Motivo de recusa criado',
                    'body' => 'O motivo de recusa foi criado com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nome',
        'template' => 'Modelo',
    ],
];
