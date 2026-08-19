<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name' => 'Nome',
                    'type' => 'Tipo',
                ],
            ],

            'options' => [
                'title' => 'Opções',

                'fields' => [
                    'name'        => 'Nome',
                    'color'       => 'Cor',
                    'extra-price' => 'Preço extra',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nome',
            'type'       => 'Tipo',
            'deleted-at' => 'Excluído em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'type'       => 'Tipo',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'type' => 'Tipo',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Atributo restaurado',
                    'body'  => 'O atributo foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atributo excluído',
                    'body'  => 'O atributo foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Atributo excluído permanentemente',
                        'body'  => 'O atributo foi excluído permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Atributo não pôde ser excluído',
                        'body'  => 'O atributo não pode ser excluído porque está em uso pelo produto: :products',
                        'more'  => '... +:count mais',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Atributos restaurados',
                    'body'  => 'Os atributos foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atributos excluídos',
                    'body'  => 'Os atributos foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Atributos excluídos permanentemente',
                        'body'  => 'Os atributos foram excluídos permanentemente com sucesso.',
                    ],

                    'partial' => [
                        'title' => 'Alguns atributos não puderam ser excluídos',
                        'body'  => 'O atributo ":attributes" não pode ser excluído porque está em uso pelo produto: :products',
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
                    'name' => 'Nome',
                    'type' => 'Tipo',
                ],
            ],

            'record-information' => [
                'title' => 'Informações do registro',

                'entries' => [
                    'creator'    => 'Criado por',
                    'created_at' => 'Criado em',
                    'updated_at' => 'Última atualização em',
                ],
            ],
        ],
    ],
];
