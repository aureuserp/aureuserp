<?php

return [
    'navigation' => [
        'title' => 'Etapas do projeto',
    ],

    'form' => [
        'name' => 'Nome',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nome',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'name'         => 'Nome',
            'is-completed' => 'Está concluído',
            'project'      => 'Projeto',
            'created-at'   => 'Criado em',
        ],

        'filters' => [
            'is-completed' => 'Está concluído',
            'project'      => 'Projeto',
            'creator'      => 'Criador',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etapa do projeto atualizado',
                    'body'  => 'O etapa do projeto foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Etapa do projeto restaurado',
                    'body'  => 'O etapa do projeto foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etapa do projeto excluído',
                    'body'  => 'O etapa do projeto foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Etapa do projeto excluído permanentemente',
                        'body'  => 'O etapa do projeto foi excluído permanentemente com sucesso.',
                    ],
                    'error' => [
                        'title' => 'Etapa do projeto não pôde ser excluído',
                        'body'  => 'O etapa do projeto não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Etapas do projeto restaurados',
                    'body'  => 'Os etapas do projeto foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etapas do projeto excluídos',
                    'body'  => 'Os etapas do projeto foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Etapas do projeto excluídos permanentemente',
                    'body'  => 'Os etapas do projeto foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],
];
