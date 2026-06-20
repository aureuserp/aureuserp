<?php

return [
    'navigation' => [
        'title' => 'Categorias',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',

                'fields' => [
                    'name' => 'Nome',
                    'technician' => 'Responsável',
                    'company' => 'Empresa',
                    'note' => 'Nota',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'technician' => 'Responsável',
            'company' => 'Empresa',
            'created-at' => 'Criado em',
        ],

        'groups' => [
            'technician' => 'Responsável',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Categoria atualizada',
                    'body' => 'A categoria foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Categoria excluída',
                    'body' => 'A categoria foi excluída com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categorias excluídas',
                    'body' => 'As categorias foram excluídas com sucesso.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Categoria criada',
                    'body' => 'A categoria foi criada com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',

                'entries' => [
                    'name' => 'Nome',
                    'technician' => 'Responsável',
                    'company' => 'Empresa',
                    'note' => 'Nota',
                ],
            ],
        ],
    ],
];
