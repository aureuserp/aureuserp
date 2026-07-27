<?php

return [
    'title' => 'Equipes de vendas',

    'navigation' => [
        'title' => 'Equipes de vendas',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name' => 'Equipe de vendas',
                'status' => 'Status',
                'fieldset' => [
                    'team-details' => [
                        'title' => 'Detalhes da equipe',
                        'fields' => [
                            'team-leader' => 'Líder da equipe',
                            'company' => 'Empresa',
                            'invoiced-target' => 'Meta faturada',
                            'invoiced-target-suffix' => '/ Mês',
                            'color' => 'Cor',
                            'members' => 'Membros',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id' => 'ID',
            'company' => 'Empresa',
            'team-leader' => 'Líder da equipe',
            'name' => 'Nome',
            'status' => 'Status',
            'invoiced-target' => 'Meta faturada',
            'color' => 'Cor',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'name' => 'Nome',
            'team-leader' => 'Líder da equipe',
            'company' => 'Empresa',
            'created-by' => 'Criado por',
            'updated-at' => 'Atualizado em',
            'created-at' => 'Criado em',
        ],

        'groups' => [
            'name' => 'Nome',
            'company' => ' Empresa',
            'team-leader' => 'Líder da equipe',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipe de vendas restaurada',
                    'body' => 'A equipe de vendas foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipe de vendas excluída',
                    'body' => 'A equipe de vendas foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Equipe de vendas excluída permanentemente',
                    'body' => 'A equipe de vendas foi excluída permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipes de vendas restauradas',
                    'body' => 'As equipes de vendas foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipes de vendas excluídas',
                    'body' => 'As equipes de vendas foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Equipes de vendas excluídas permanentemente',
                    'body' => 'As equipes de vendas foram excluídas permanentemente com sucesso.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Equipes de vendas criadas',
                    'body' => 'As equipes de vendas foram criadas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name' => 'Equipe de vendas',
                'status' => 'Status',
                'fieldset' => [
                    'team-details' => [
                        'title' => 'Detalhes da equipe',
                        'entries' => [
                            'team-leader' => 'Líder da equipe',
                            'company' => 'Empresa',
                            'invoiced-target' => 'Meta faturada',
                            'invoiced-target-suffix' => '/ Mês',
                            'color' => 'Cor',
                            'members' => 'Membros',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
