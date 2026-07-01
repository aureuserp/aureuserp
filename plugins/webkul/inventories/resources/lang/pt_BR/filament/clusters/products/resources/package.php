<?php

return [
    'navigation' => [
        'title' => 'Embalagens',
        'group' => 'Estoque',
    ],

    'global-search' => [
        'name' => 'Nome',
        'package-type' => 'Tipo de embalagem',
        'location' => 'Localização',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'ex.: PACK007',
                    'package-type' => 'Tipo de embalagem',
                    'pack-date' => 'Data de embalagem',
                    'location' => 'Localização',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'package-type' => 'Tipo de embalagem',
            'location' => 'Localização',
            'company' => 'Empresa',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'package-type' => 'Tipo de embalagem',
            'location' => 'Localização',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'package-type' => 'Tipo de embalagem',
            'location' => 'Localização',
            'creator' => 'Criador',
            'company' => 'Empresa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Embalagem excluída',
                        'body' => 'A embalagem foi excluída com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir a embalagem',
                        'body' => 'A embalagem não pode ser excluída porque está em uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print-without-content' => [
                'label' => 'Imprimir código de barras',
            ],

            'print-with-content' => [
                'label' => 'Imprimir código de barras com conteúdo',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Embalagens excluídas',
                        'body' => 'As embalagens foram excluídas com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir as embalagens',
                        'body' => 'As embalagens não podem ser excluídas porque estão em uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Detalhes da embalagem',

                'entries' => [
                    'name' => 'Nome da embalagem',
                    'package-type' => 'Tipo de embalagem',
                    'pack-date' => 'Data de embalagem',
                    'location' => 'Localização',
                    'company' => 'Empresa',
                    'created-at' => 'Criado em',
                    'updated-at' => 'Última atualização',
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
