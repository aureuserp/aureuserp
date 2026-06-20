<?php

return [
    'navigation' => [
        'title' => 'Tipos de operação',
        'group' => 'Gestão de armazéns',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'operator-type' => 'Tipo de operação',
                    'operator-type-placeholder' => 'ex.: Recebimentos',
                ],
            ],

            'applicable-on' => [
                'title' => 'Aplicável a',
                'description' => 'Selecione os locais onde esta rota pode ser selecionada.',

                'fields' => [
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'operator-type' => 'Tipo de operação',
                    'sequence-prefix' => 'Prefixo da sequência',
                    'generate-shipping-labels' => 'Gerar etiquetas de envio',
                    'warehouse' => 'Armazém',
                    'show-reception-report' => 'Mostrar relatório de recebimento na validação',
                    'show-reception-report-hint-tooltip' => 'Se selecionado, o sistema exibirá automaticamente o relatório de recebimento na validação, desde que haja movimentações para alocar.',
                    'company' => 'Empresa',
                    'return-type' => 'Tipo de devolução',
                    'create-backorder' => 'Criar pedido pendente',
                    'move-type' => 'Tipo de lançamento',
                    'move-type-hint-tooltip' => 'A menos que definido pelo documento de origem, isto servirá como a política padrão de separação para este tipo de operação.',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title' => 'Lotes/Números de série',

                        'fields' => [
                            'create-new' => 'Criar novo',
                            'create-new-hint-tooltip' => 'Se selecionado, o sistema assumirá que você pretende criar novos lotes/números de série, permitindo informá-los em um campo de texto.',
                            'use-existing' => 'Usar existente',
                            'use-existing-hint-tooltip' => 'Se selecionado, você pode escolher os lotes/números de série ou optar por não atribuir nenhum. Isso permite criar estoque sem lote ou sem restrições sobre o lote usado.',
                        ],
                    ],

                    'locations' => [
                        'title' => 'Locais',

                        'fields' => [
                            'source-location' => 'Local de origem',
                            'source-location-hint-tooltip' => 'Este é o local de origem padrão ao criar esta operação manualmente. No entanto, ele pode ser alterado depois, e as rotas podem atribuir outro local padrão.',
                            'destination-location' => 'Local de destino',
                            'destination-location-hint-tooltip' => 'Este é o local de destino padrão para operações criadas manualmente. No entanto, ele pode ser modificado depois, e as rotas podem atribuir outro local padrão.',
                        ],
                    ],

                    'packages' => [
                        'title' => 'Embalagens',

                        'fields' => [
                            'show-entire-package' => 'Mover embalagem inteira',
                            'show-entire-package-hint-tooltip' => 'Se selecionado, você pode mover embalagens inteiras.',
                        ],
                    ],
                ],
            ],

            'hardware' => [
                'title' => 'Hardware',

                'fieldsets' => [
                    'print-on-validation' => [
                        'title' => 'Imprimir na validação',

                        'fields' => [
                            'delivery-slip' => 'Comprovante de entrega',
                            'delivery-slip-hint-tooltip' => 'Se selecionado, o sistema imprimirá automaticamente o comprovante de entrega quando a separação for validada.',

                            'return-slip' => 'Comprovante de devolução',
                            'return-slip-hint-tooltip' => 'Se selecionado, o sistema imprimirá automaticamente o comprovante de devolução quando a separação for validada.',

                            'product-labels' => 'Etiquetas de produto',
                            'product-labels-hint-tooltip' => 'Se selecionado, o sistema imprimirá automaticamente as etiquetas de produto quando a separação for validada.',

                            'lots-labels' => 'Etiquetas de lote/NS',
                            'lots-labels-hint-tooltip' => 'Se selecionado, o sistema imprimirá automaticamente as etiquetas de lote/número de série quando a separação for validada.',

                            'reception-report' => 'Relatório de recebimento',
                            'reception-report-hint-tooltip' => 'Se selecionado, o sistema imprimirá automaticamente o relatório de recebimento quando a separação for validada e contiver movimentações atribuídas.',

                            'reception-report-labels' => 'Etiquetas do relatório de recebimento',
                            'reception-report-labels-hint-tooltip' => 'Se selecionado, o sistema imprimirá automaticamente as etiquetas do relatório de recebimento quando a separação for validada.',

                            'package-content' => 'Conteúdo da embalagem',
                            'package-content-hint-tooltip' => 'Se selecionado, o sistema imprimirá automaticamente os detalhes da embalagem e seu conteúdo quando a separação for validada.',
                        ],
                    ],

                    'print-on-pack' => [
                        'title' => 'Imprimir em "Colocar na embalagem"',

                        'fields' => [
                            'package-label' => 'Etiqueta da embalagem',
                            'package-label-hint-tooltip' => 'Se selecionado, o sistema imprimirá automaticamente a etiqueta da embalagem quando o botão "Colocar na embalagem" for usado.',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'warehouse' => 'Armazém',
            'company' => 'Empresa',
            'deleted-at' => 'Excluído em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'type' => 'Tipo',
            'warehouse' => 'Armazém',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'type' => 'Tipo',
            'warehouse' => 'Armazém',
            'company' => 'Empresa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de operação restaurado',
                    'body' => 'O tipo de operação foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de operação excluído',
                    'body' => 'O tipo de operação foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipo de operação excluído permanentemente',
                        'body' => 'O tipo de operação foi excluído permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir o tipo de operação',
                        'body' => 'O tipo de operação não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipos de operação restaurados',
                    'body' => 'Os tipos de operação foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipos de operação excluídos',
                    'body' => 'Os tipos de operação foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipos de operação excluídos permanentemente',
                        'body' => 'Os tipos de operação foram excluídos permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir os tipos de operação',
                        'body' => 'Os tipos de operação não podem ser excluídos porque estão em uso no momento.',
                    ],
                ],
            ],
        ],

        'empty-actions' => [
            'create' => [
                'label' => 'Criar tipo de operação',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',

                'entries' => [
                    'name' => 'Nome',
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

        'tabs' => [
            'general' => [
                'title' => 'Geral',

                'entries' => [
                    'type' => 'Tipo de operação',
                    'sequence_code' => 'Código da sequência',
                    'print_label' => 'Imprimir etiqueta',
                    'warehouse' => 'Armazém',
                    'reservation_method' => 'Método de reserva',
                    'auto_show_reception_report' => 'Mostrar relatório de recebimento automaticamente',
                    'company' => 'Empresa',
                    'return_operation_type' => 'Tipo de operação de devolução',
                    'create_backorder' => 'Criar pedido pendente',
                    'move_type' => 'Tipo de lançamento',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title' => 'Lotes',

                        'entries' => [
                            'use_create_lots' => 'Usar criação de lotes',
                            'use_existing_lots' => 'Usar lotes existentes',
                        ],
                    ],

                    'locations' => [
                        'title' => 'Locais',

                        'entries' => [
                            'source_location' => 'Local de origem',
                            'destination_location' => 'Local de destino',
                        ],
                    ],
                ],
            ],
            'hardware' => [
                'title' => 'Hardware',

                'fieldsets' => [
                    'print_on_validation' => [
                        'title' => 'Imprimir na validação',

                        'entries' => [
                            'auto_print_delivery_slip' => 'Imprimir comprovante de entrega automaticamente',
                            'auto_print_return_slip' => 'Imprimir comprovante de devolução automaticamente',
                            'auto_print_product_labels' => 'Imprimir etiquetas de produto automaticamente',
                            'auto_print_lot_labels' => 'Imprimir etiquetas de lote automaticamente',
                            'auto_print_reception_report' => 'Imprimir relatório de recebimento automaticamente',
                            'auto_print_reception_report_labels' => 'Imprimir etiquetas do relatório de recebimento automaticamente',
                            'auto_print_packages' => 'Imprimir embalagens automaticamente',
                        ],
                    ],

                    'print_on_pack' => [
                        'title' => 'Imprimir ao embalar',

                        'entries' => [
                            'auto_print_package_label' => 'Imprimir etiqueta da embalagem automaticamente',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
