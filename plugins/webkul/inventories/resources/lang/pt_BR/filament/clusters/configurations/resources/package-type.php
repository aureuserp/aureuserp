<?php

return [
    'navigation' => [
        'title' => 'Tipos de embalagem',
        'group' => 'Entrega',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name' => 'Nome',
                    'barcode' => 'Código de barras',
                    'company' => 'Empresa',
                    'weight' => 'Peso',
                    'max-weight' => 'Peso máximo',

                    'fieldsets' => [
                        'size' => [
                            'title' => 'Tamanho',

                            'fields' => [
                                'length' => 'Comprimento',
                                'width' => 'Largura',
                                'height' => 'Altura',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'barcode' => 'Código de barras',
            'weight' => 'Peso',
            'max-weight' => 'Peso máximo',
            'width' => 'Largura',
            'height' => 'Altura',
            'length' => 'Comprimento',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipo de embalagem excluído',
                    'body' => 'O tipo de embalagem foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipo de embalagem excluído',
                    'body' => 'O tipo de embalagem foi excluído com sucesso.',
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
                    'fieldsets' => [
                        'size' => [
                            'title' => 'Dimensões da embalagem',
                            'entries' => [
                                'length' => 'Comprimento',
                                'width' => 'Largura',
                                'height' => 'Altura',
                            ],
                        ],
                    ],
                    'weight' => 'Peso base',
                    'max-weight' => 'Peso máximo',
                    'barcode' => 'Código de barras',
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
