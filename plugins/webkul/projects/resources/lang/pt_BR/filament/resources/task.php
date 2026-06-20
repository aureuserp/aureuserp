<?php

return [
    'title' => 'Tarefas',

    'navigation' => [
        'title' => 'Tarefas',
        'group' => 'Projeto',
    ],

    'global-search' => [
        'project' => 'Projeto',
        'customer' => 'Cliente',
        'milestone' => 'Marco',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'title' => 'Título',
                    'title-placeholder' => 'Título da tarefa...',
                    'tags' => 'Tags',
                    'name' => 'Nome',
                    'color' => 'Cor',
                    'description' => 'Descrição',
                    'project' => 'Projeto',
                    'status' => 'Status',
                    'start_date' => 'Data de início',
                    'end_date' => 'Data de término',
                ],
            ],

            'additional' => [
                'title' => 'Informações adicionais',
            ],

            'settings' => [
                'title' => 'Configurações',

                'fields' => [
                    'project' => 'Projeto',
                    'milestone' => 'Marco',
                    'milestone-hint-text' => 'Entregue automaticamente seus serviços ao atingir um marco vinculando-o a um item do pedido de venda.',
                    'name' => 'Nome',
                    'deadline' => 'Prazo final',
                    'is-completed' => 'Está concluído',
                    'customer' => 'Cliente',
                    'assignees' => 'Responsáveis',
                    'allocated-hours' => 'Horas alocadas',
                    'allocated-hours-helper-text' => 'Em horas (Ex.: 1,5 horas significa 1 hora e 30 minutos)',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id' => 'ID',
            'priority' => 'Prioridade',
            'state' => 'Estado',
            'new-state' => 'Novo estado',
            'update-state' => 'Atualizar estado',
            'title' => 'Título',
            'project' => 'Projeto',
            'project-placeholder' => 'Tarefa privada',
            'milestone' => 'Marco',
            'customer' => 'Cliente',
            'assignees' => 'Responsáveis',
            'allocated-time' => 'Tempo alocado',
            'time-spent' => 'Tempo gasto',
            'time-remaining' => 'Tempo restante',
            'progress' => 'Progresso',
            'deadline' => 'Prazo final',
            'tags' => 'Tags',
            'stage' => 'Estágio',
        ],

        'actions' => [
            'update-state' => [
                'modal-heading' => 'Atualizar estado da tarefa',
            ],
        ],

        'groups' => [
            'state' => 'Estado',
            'project' => 'Projeto',
            'milestone' => 'Marco',
            'customer' => 'Cliente',
            'deadline' => 'Prazo final',
            'stage' => 'Estágio',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'title' => 'Título',
            'priority' => 'Prioridade',
            'low' => 'Baixo',
            'high' => 'Alto',
            'state' => 'Estado',
            'tags' => 'Tags',
            'allocated-hours' => 'Horas alocadas',
            'total-hours-spent' => 'Total de horas gastas',
            'remaining-hours' => 'Horas restantes',
            'overtime' => 'Hora extra',
            'progress' => 'Progresso',
            'deadline' => 'Prazo final',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
            'assignees' => 'Responsáveis',
            'customer' => 'Cliente',
            'project' => 'Projeto',
            'stage' => 'Estágio',
            'milestone' => 'Marco',
            'company' => 'Empresa',
            'creator' => 'Criador',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tarefa restaurada',
                    'body' => 'A tarefa foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tarefa excluída',
                    'body' => 'A tarefa foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tarefa excluída permanentemente',
                    'body' => 'A tarefa foi excluída permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tarefas restauradas',
                    'body' => 'As tarefas foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tarefas excluídas',
                    'body' => 'As tarefas foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tarefas excluídas permanentemente',
                    'body' => 'As tarefas foram excluídas permanentemente com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'entries' => [
                    'title' => 'Título',
                    'state' => 'Estado',
                    'tags' => 'Tags',
                    'priority' => 'Prioridade',
                    'description' => 'Descrição',
                ],
            ],

            'project-information' => [
                'title' => 'Informações do projeto',

                'entries' => [
                    'project' => 'Projeto',
                    'milestone' => 'Marco',
                    'customer' => 'Cliente',
                    'assignees' => 'Responsáveis',
                    'deadline' => 'Prazo final',
                    'stage' => 'Estágio',
                ],
            ],

            'time-tracking' => [
                'title' => 'Controle de tempo',

                'entries' => [
                    'allocated-time' => 'Tempo alocado',
                    'time-spent' => 'Tempo gasto',
                    'time-spent-suffix' => ' horas',
                    'time-remaining' => 'Tempo restante',
                    'time-remaining-suffix' => ' horas',
                    'progress' => 'Progresso',
                ],
            ],

            'additional-information' => [
                'title' => 'Informações adicionais',
            ],

            'record-information' => [
                'title' => 'Informações do registro',

                'entries' => [
                    'created-at' => 'Criado em',
                    'created-by' => 'Criado por',
                    'last-updated' => 'Última atualização',
                ],
            ],

            'statistics' => [
                'title' => 'Estatísticas',

                'entries' => [
                    'sub-tasks' => 'Subtarefas',
                    'timesheet-entries' => 'Entradas de apontamento de horas',
                ],
            ],
        ],
    ],
];
