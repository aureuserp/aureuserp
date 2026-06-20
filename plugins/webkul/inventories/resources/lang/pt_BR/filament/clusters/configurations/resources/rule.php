<?php

return [
    'navigation' => [
        'title' => 'Regras',
        'group' => 'Gestão de armazéns',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name' => 'Nome',
                    'action' => 'Ação',
                    'operation-type' => 'Tipo de operação',
                    'source-location' => 'Local de origem',
                    'destination-location' => 'Local de destino',
                    'supply-method' => 'Método de suprimento',
                    'supply-method-hint-tooltip' => 'Retirar do estoque: os produtos são obtidos diretamente do estoque disponível no local de origem.<br/>Acionar outra regra: o sistema ignora o estoque disponível e procura uma regra de estoque para reabastecer o local de origem.<br/>Retirar do estoque; se indisponível, acionar outra regra: os produtos são retirados primeiro do estoque disponível. Se nenhum estiver disponível, o sistema aplica uma regra de estoque para trazer produtos ao local de origem.',
                    'automatic-move' => 'Movimentação automática',
                    'automatic-move-hint-tooltip' => 'Operação manual: cria uma movimentação de estoque separada após a atual.<br/>Automático sem etapa adicionada: substitui diretamente o local na movimentação original sem adicionar uma etapa extra.',

                    'action-information' => [
                        'pull' => 'Quando produtos são necessários em <b>:sourceLocation</b>, :operation é gerada a partir de <b>:destinationLocation</b> para atender à demanda.',
                        'push' => 'Quando produtos chegam a <b>:sourceLocation</b>,</br><b>:operation</b> é gerada para transferi-los para <b>:destinationLocation</b>.',
                        'buy' => 'Quando produtos são necessários em <b>:destinationLocation</b>, uma solicitação de cotação é criada para atender à necessidade.',
                        'manufacture' => 'Quando produtos são necessários em <b>:destinationLocation</b>, uma ordem de fabricação é criada para atender à necessidade.',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Configurações',

                'fields' => [
                    'partner-address' => 'Endereço do parceiro',
                    'partner-address-hint-tooltip' => 'Endereço onde as mercadorias devem ser entregues. Opcional.',
                    'lead-time' => 'Prazo (dias)',
                    'lead-time-hint-tooltip' => 'A data prevista da transferência será calculada usando este prazo.',
                ],

                'fieldsets' => [
                    'applicability' => [
                        'title' => 'Aplicabilidade',

                        'fields' => [
                            'route' => 'Rota',
                            'company' => 'Empresa',
                        ],
                    ],

                    'propagation' => [
                        'title' => 'Propagação',

                        'fields' => [
                            'propagation-procurement-group' => 'Propagação do grupo de aquisição',
                            'propagation-procurement-group-hint-tooltip' => 'Se selecionado, cancelar a movimentação criada por esta regra também cancelará a movimentação subsequente.',
                            'cancel-next-move' => 'Cancelar próxima movimentação',
                            'warehouse-to-propagate' => 'Armazém a propagar',
                            'warehouse-to-propagate-hint-tooltip' => 'O armazém atribuído à movimentação ou aquisição criada, que pode diferir do armazém ao qual esta regra se aplica (ex.: para regras de ressuprimento a partir de outro armazém).',
                        ],
                    ],
                ],

            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'action' => 'Ação',
            'source-location' => 'Local de origem',
            'destination-location' => 'Local de destino',
            'route' => 'Rota',
            'deleted-at' => 'Excluído em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'action' => 'Ação',
            'source-location' => 'Local de origem',
            'destination-location' => 'Local de destino',
            'route' => 'Rota',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'action' => 'Ação',
            'source-location' => 'Local de origem',
            'destination-location' => 'Local de destino',
            'route' => 'Rota',
            'company' => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Regra atualizada',
                    'body' => 'A regra foi atualizada com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Regra restaurada',
                    'body' => 'A regra foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Regra excluída',
                    'body' => 'A regra foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Regra excluída permanentemente',
                        'body' => 'A regra foi excluída permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir a regra',
                        'body' => 'A regra não pode ser excluída porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Regras restauradas',
                    'body' => 'As regras foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Regras excluídas',
                    'body' => 'As regras foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Regras excluídas permanentemente',
                        'body' => 'As regras foram excluídas permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir as regras',
                        'body' => 'As regras não podem ser excluídas porque estão em uso no momento.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Detalhes da regra',

                'description' => [
                    'pull' => 'Quando produtos são necessários em <b>:sourceLocation</b>, <b>:operation</b> é gerada a partir de <b>:destinationLocation</b> para atender à demanda.',
                    'push' => 'Quando produtos chegam a <b>:sourceLocation</b>, <b>:operation</b> é gerada para transferi-los para <b>:destinationLocation</b>.',
                ],

                'entries' => [
                    'name' => 'Nome da regra',
                    'action' => 'Ação',
                    'operation-type' => 'Tipo de operação',
                    'source-location' => 'Local de origem',
                    'destination-location' => 'Local de destino',
                    'route' => 'Rota',
                    'company' => 'Empresa',
                    'partner-address' => 'Endereço do parceiro',
                    'lead-time' => 'Prazo de entrega',
                    'action-information' => 'Informações da ação',
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
