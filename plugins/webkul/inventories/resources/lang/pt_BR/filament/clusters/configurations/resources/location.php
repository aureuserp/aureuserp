<?php

return [
    'navigation' => [
        'title' => 'Locais',
        'group' => 'Gestão de armazéns',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'location' => 'Localização',
                    'location-placeholder' => 'ex.: Estoque reserva',
                    'parent-location' => 'Local pai',
                    'parent-location-hint-tooltip' => 'O local principal que abrange este local. Por exemplo, a \'Zona de expedição\' faz parte do local pai \'Portão 1\'.',
                    'external-notes' => 'Notas externas',
                ],
            ],

            'settings' => [
                'title' => 'Configurações',

                'fields' => [
                    'location-type' => 'Tipo de local',
                    'company' => 'Empresa',
                    'storage-category' => 'Categoria de armazenamento',
                    'is-scrap' => 'É um local de sucata?',
                    'is-scrap-hint-tooltip' => 'Marque esta caixa para designar este local para armazenar produtos descartados ou danificados.',
                    'is-dock' => 'É um local de doca?',
                    'is-dock-hint-tooltip' => 'Marque esta caixa para designar este local para armazenar mercadorias prontas para envio.',
                    'is-replenish' => 'É um local de reabastecimento?',
                    'is-replenish-hint-tooltip' => 'Ative esta função para obter todas as quantidades necessárias para reabastecimento neste local.',
                    'logistics' => 'Logística',
                    'removal-strategy' => 'Estratégia de remoção',
                    'removal-strategy-hint-tooltip' => 'Especifica o método padrão para determinar a prateleira, o lote e o local exatos de onde os produtos serão separados. Este método pode ser imposto no nível da categoria do produto, com fallback para locais pai se não definido aqui.',
                    'cyclic-counting' => 'Contagem cíclica',
                    'inventory-frequency' => 'Frequência de inventário',
                    'last-inventory' => 'Último inventário',
                    'last-inventory-hint-tooltip' => 'Data do último inventário neste local.',
                    'next-expected' => 'Próximo previsto',
                    'next-expected-hint-tooltip' => 'Data do próximo inventário planejado com base no cronograma cíclico.',
                ],
            ],

            'additional' => [
                'title' => 'Informações adicionais',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'location' => 'Localização',
            'type' => 'Tipo',
            'storage-category' => 'Categoria de armazenamento',
            'company' => 'Empresa',
            'deleted-at' => 'Excluído em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'warehouse' => 'Armazém',
            'type' => 'Tipo',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'location' => 'Localização',
            'type' => 'Tipo',
            'company' => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Local atualizado',
                    'body' => 'O local foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Local restaurado',
                    'body' => 'O local foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Local excluído',
                    'body' => 'O local foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Local excluído permanentemente',
                        'body' => 'O local foi excluído permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir o local',
                        'body' => 'O local não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimir código de barras',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Locais restaurados',
                    'body' => 'Os locais foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Locais excluídos',
                    'body' => 'Os locais foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Locais excluídos permanentemente',
                        'body' => 'Os locais foram excluídos permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir os locais',
                        'body' => 'Os locais não podem ser excluídos porque estão em uso no momento.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'entries' => [
                    'location' => 'Localização',
                    'location-placeholder' => 'ex.: Estoque reserva',
                    'parent-location' => 'Local pai',
                    'parent-location-hint-tooltip' => 'O local principal que abrange este local. Por exemplo, a \'Zona de expedição\' faz parte do local pai \'Portão 1\'.',
                    'external-notes' => 'Notas externas',
                ],
            ],

            'settings' => [
                'title' => 'Configurações',

                'entries' => [
                    'location-type' => 'Tipo de local',
                    'company' => 'Empresa',
                    'storage-category' => 'Categoria de armazenamento',
                    'is-scrap' => 'É um local de sucata?',
                    'is-scrap-hint-tooltip' => 'Marque esta caixa para designar este local para armazenar produtos descartados ou danificados.',
                    'is-dock' => 'É um local de doca?',
                    'is-dock-hint-tooltip' => 'Marque esta caixa para designar este local para armazenar mercadorias prontas para envio.',
                    'is-replenish' => 'É um local de reabastecimento?',
                    'is-replenish-hint-tooltip' => 'Ative esta função para obter todas as quantidades necessárias para reabastecimento neste local.',
                    'logistics' => 'Logística',
                    'removal-strategy' => 'Estratégia de remoção',
                    'removal-strategy-hint-tooltip' => 'Especifica o método padrão para determinar a prateleira, o lote e o local exatos de onde os produtos serão separados. Este método pode ser imposto no nível da categoria do produto, com fallback para locais pai se não definido aqui.',
                    'cyclic-counting' => 'Contagem cíclica',
                    'inventory-frequency' => 'Frequência de inventário',
                    'last-inventory' => 'Último inventário',
                    'last-inventory-hint-tooltip' => 'Data do último inventário neste local.',
                    'next-expected' => 'Próximo previsto',
                    'next-expected-hint-tooltip' => 'Data do próximo inventário planejado com base no cronograma cíclico.',
                ],
            ],

            'additional' => [
                'title' => 'Informações adicionais',
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
