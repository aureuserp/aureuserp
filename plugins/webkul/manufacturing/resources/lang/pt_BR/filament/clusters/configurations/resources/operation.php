<?php

return [
    'navigation' => [
        'title' => 'Operações',
        'group' => 'Configuração',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',
                'fields' => [
                    'name' => 'Operação',
                    'name-placeholder' => 'ex.: Corte',
                    'bill-of-material' => 'Lista de materiais',
                    'work-center' => 'Centro de trabalho',
                    'apply-on-variants' => 'Aplicar nas variantes',
                    'company' => 'Empresa',
                    'blocked-by' => 'Bloqueado por',
                ],
            ],
            'settings' => [
                'title' => 'Configurações',
                'fields' => [
                    'time-mode' => 'Cálculo da duração',
                    'time-mode-batch' => 'Baseado em',
                    'time-mode-batch-prefix' => 'último',
                    'time-mode-batch-suffix' => 'ordens de trabalho',
                    'manual-cycle-time' => 'Duração padrão',
                    'manual-cycle-time-suffix' => 'minutos',
                ],
            ],
            'worksheet' => [
                'title' => 'Folha de trabalho',
                'fields' => [
                    'worksheet' => 'Folha de trabalho',
                    'pdf' => 'PDF',
                    'google-slide' => 'Google Slide',
                    'google-slide-placeholder' => 'Link do Google Slide',
                    'description' => 'Descrição',
                    'description-placeholder' => 'Descrição da operação...',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Operação',
            'bill-of-material' => 'Lista de materiais',
            'work-center' => 'Centro de trabalho',
            'time-mode' => 'Cálculo da duração',
            'manual-cycle-time' => 'Duração padrão',
            'worksheet-type' => 'Folha de trabalho',
            'deleted-at' => 'Excluído em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],
        'filters' => [
            'work-center' => 'Centro de trabalho',
            'time-mode' => 'Cálculo da duração',
            'worksheet-type' => 'Folha de trabalho',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operação restaurada',
                    'body' => 'A operação foi restaurada com sucesso.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operação arquivada',
                    'body' => 'A operação foi arquivada com sucesso.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operação excluída',
                        'body' => 'A operação foi excluída permanentemente.',
                    ],
                    'error' => [
                        'title' => 'Não foi possível excluir a operação',
                        'body' => 'A operação não pode ser excluída porque está em uso no momento.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operações restauradas',
                    'body' => 'As operações selecionadas foram restauradas com sucesso.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operações arquivadas',
                    'body' => 'As operações selecionadas foram arquivadas com sucesso.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operações excluídas',
                        'body' => 'As operações selecionadas foram excluídas permanentemente.',
                    ],
                    'error' => [
                        'title' => 'Não foi possível excluir as operações',
                        'body' => 'Uma ou mais operações selecionadas estão em uso no momento.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',
                'entries' => [
                    'name' => 'Operação',
                    'bill-of-material' => 'Lista de materiais',
                    'work-center' => 'Centro de trabalho',
                    'apply-on-variants' => 'Aplicar nas variantes',
                    'company' => 'Empresa',
                ],
            ],
            'settings' => [
                'title' => 'Configurações',
                'entries' => [
                    'time-mode' => 'Cálculo da duração',
                    'time-mode-batch' => 'Baseado em',
                    'manual-cycle-time' => 'Duração padrão',
                    'manual-cycle-time-suffix' => 'minutos',
                ],
            ],
            'worksheet' => [
                'title' => 'Folha de trabalho',
                'entries' => [
                    'worksheet' => 'Folha de trabalho',
                    'pdf' => 'PDF',
                    'google-slide' => 'Google Slide',
                    'description' => 'Descrição',
                ],
            ],
            'record-information' => [
                'title' => 'Informações do registro',
                'entries' => [
                    'created-by' => 'Criado por',
                    'created-at' => 'Criado em',
                    'last-updated' => 'Última atualização',
                ],
            ],
        ],
    ],
];
