<?php

return [
    'permission-descriptions' => [
        'view_any'         => 'Listar e navegar por todos os registros.',
        'view'             => 'Ver um registro em detalhe.',
        'create'           => 'Criar novos registros.',
        'update'           => 'Editar e salvar registros existentes.',
        'delete'           => 'Excluir um registro.',
        'delete_any'       => 'Excluir vários registros de uma vez.',
        'force_delete'     => 'Excluir permanentemente um registro (sem lixeira).',
        'force_delete_any' => 'Excluir permanentemente vários registros.',
        'restore'          => 'Restaurar um registro arquivado.',
        'restore_any'      => 'Restaurar vários registros arquivados.',
        'reorder'          => 'Alterar a ordem de classificação dos registros.',
    ],

    'form' => [
        'fields' => [
            'web'     => 'Web',
            'sanctum' => 'Sanctum',
        ],
    ],

    'notification' => [
        'system-role-delete' => [
            'title' => 'Função do sistema não pode ser excluída',
            'body'  => 'Esta é uma função do sistema e não pode ser excluída.',
        ],
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Função excluída',
                    'body'  => 'A função foi excluída com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Funções excluídas',
                    'body'  => 'As funções selecionadas foram excluídas com sucesso.',
                ],
            ],
        ],
    ],
];
