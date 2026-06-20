<?php

return [
    'navigation' => [
        'title' => 'Armazéns',
        'group' => 'Gestão de armazéns',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',
                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'ex.: Armazém central',
                    'code' => 'Nome curto',
                    'code-placeholder' => 'ex.: AC',
                    'code-hint-tooltip' => 'O nome curto serve como identificador do armazém.',
                    'company' => 'Empresa',
                    'address' => 'Endereço',
                ],
            ],

            'settings' => [
                'title' => 'Configurações',

                'fields' => [
                    'shipment-management' => 'Gestão de envios',
                    'incoming-shipments' => 'Recebimentos',
                    'incoming-shipments-hint-tooltip' => 'Rota padrão de recebimento a seguir',
                    'outgoing-shipments' => 'Entregas',
                    'outgoing-shipments-hint-tooltip' => 'Rota padrão de entrega a seguir',
                    'manufacture' => 'Produção',
                    'manufacture-hint-tooltip' => 'Rota padrão de fabricação a seguir',
                    'resupply-management' => 'Gestão de reabastecimento',
                    'resupply-management-hint-tooltip' => 'As rotas serão geradas automaticamente para reabastecer este armazém a partir dos armazéns selecionados.',
                    'resupply-from' => 'Reabastecer de',
                ],
            ],

            'additional' => [
                'title' => 'Informações adicionais',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'code' => 'Nome curto',
            'company' => 'Empresa',
            'address' => 'Endereço',
            'deleted-at' => 'Excluído em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'address' => 'Endereço',
            'company' => 'Empresa',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'company' => 'Empresa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Armazém restaurado',
                    'body' => 'O armazém foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Armazém excluído',
                    'body' => 'O armazém foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Armazém excluído permanentemente',
                        'body' => 'O armazém foi excluído permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir o armazém',
                        'body' => 'O armazém não pode ser excluído porque está em uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Armazéns restaurados',
                    'body' => 'Os armazéns foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Armazéns excluídos',
                    'body' => 'Os armazéns foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Armazéns excluídos permanentemente',
                        'body' => 'Os armazéns foram excluídos permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir os armazéns',
                        'body' => 'Os armazéns não podem ser excluídos porque estão em uso.',
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
                    'name' => 'Nome do armazém',
                    'code' => 'Código do armazém',
                    'company' => 'Empresa',
                    'address' => 'Endereço',
                ],
            ],

            'settings' => [
                'title' => 'Configurações',

                'entries' => [
                    'shipment-management' => 'Gestão de envios',
                    'incoming-shipments' => 'Recebimentos',
                    'outgoing-shipments' => 'Entregas',
                    'resupply-management' => 'Gestão de reabastecimento',
                    'resupply-from' => 'Reabastecer de',
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
