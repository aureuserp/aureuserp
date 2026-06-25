<?php

return [
    'title' => 'Locais de trabalho',

    'navigation' => [
        'title' => 'Locais de trabalho',
        'group' => 'Colaborador',
    ],

    'form' => [
        'name'            => 'Nome',
        'company'         => 'Empresa',
        'location-type'   => 'Tipo de local',
        'location-number' => 'Número do local',
        'status'          => 'Status',
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'name'            => 'Nome',
            'status'          => 'Status',
            'company'         => 'Empresa',
            'location-type'   => 'Tipo de local',
            'location-number' => 'Número do local',
            'deleted-at'      => 'Excluído em',
            'created-by'      => 'Criado por',
            'created-at'      => 'Criado em',
            'updated-at'      => 'Atualizado em',
        ],

        'filters' => [
            'name'            => 'Nome',
            'status'          => 'Status',
            'created-by'      => 'Criado por',
            'company'         => 'Empresa',
            'location-number' => 'Número do local',
            'location-type'   => 'Tipo de local',
            'updated-at'      => 'Atualizado em',
            'created-at'      => 'Criado em',
        ],

        'groups' => [
            'name'          => 'Nome',
            'status'        => 'Status',
            'location-type' => 'Tipo de local',
            'company'       => 'Empresa',
            'created-by'    => 'Criado por',
            'created-at'    => 'Criado em',
            'updated-at'    => 'Atualizado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Local de trabalho atualizado',
                    'body'  => 'O local de trabalho foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Local de trabalho restaurado',
                    'body'  => 'O local de trabalho foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Local de trabalho excluído',
                    'body'  => 'O local de trabalho foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Local de trabalho excluído permanentemente',
                    'body'  => 'O local de trabalho foi excluído permanentemente com sucesso.',
                ],
            ],

            'empty-state' => [
                'notification' => [
                    'title' => 'Local de trabalho criado',
                    'body'  => 'O local de trabalho foi criado com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Locais de trabalho excluídos',
                    'body'  => 'Os locais de trabalho foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Locais de trabalho excluídos permanentemente',
                    'body'  => 'Os locais de trabalho foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'            => 'Nome',
        'company'         => 'Empresa',
        'location-type'   => 'Tipo de local',
        'location-number' => 'Número do local',
        'status'          => 'Status',
    ],
];
