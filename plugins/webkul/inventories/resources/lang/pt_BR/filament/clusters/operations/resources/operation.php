<?php

return [
    'navigation' => [
        'title' => 'Produtos',
        'group' => 'Estoque',
    ],

    'global-search' => [
        'partner' => 'Parceiro',
        'origin'  => 'Origem',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'receive-from'         => 'Receber de',
                    'contact'              => 'Contato',
                    'delivery-address'     => 'Endereço de entrega',
                    'operation-type'       => 'Tipo de operação',
                    'source-location'      => 'Local de origem',
                    'destination-location' => 'Local de destino',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title' => 'Operações',

                'columns' => [
                    'product'                    => 'Produto',
                    'final-location'             => 'Local final',
                    'description'                => 'Descrição',
                    'scheduled-at'               => 'Agendado em',
                    'deadline'                   => 'Prazo final',
                    'packaging'                  => 'Embalagem',
                    'demand'                     => 'Demanda',
                    'quantity'                   => 'Quantidade',
                    'insufficient-stock-tooltip' => 'Quantidade disponível insuficiente',
                    'unit'                       => 'Unidade',
                    'picked'                     => 'Separado',
                ],

                'actions' => [
                    'open-product' => [
                        'tooltip' => 'Abrir produto',
                    ],
                ],

                'fields' => [
                    'product'        => 'Produto',
                    'final-location' => 'Local final',
                    'description'    => 'Descrição',
                    'scheduled-at'   => 'Agendado em',
                    'deadline'       => 'Prazo final',
                    'packaging'      => 'Embalagem',
                    'demand'         => 'Demanda',
                    'quantity'       => 'Quantidade',
                    'unit'           => 'Unidade',
                    'picked'         => 'Separado',

                    'lines' => [
                        'modal-heading' => 'Gerenciar movimentações de estoque',
                        'modal-submit-action-label' => 'Salvar',
                        'add-line'      => 'Adicionar linha',

                        'actions' => [
                            'generate' => 'Gerar séries/lotes',
                            'import'   => 'Importar séries/lotes',
                        ],

                        'fields' => [
                            'lot'                => 'Número de lote/série',
                            'pick-from'          => 'Separar de',
                            'location'           => 'Armazenar em',
                            'package'            => 'Embalagem de destino',
                            'quantity'           => 'Quantidade',
                            'uom'                => 'Unidade de medida',
                            'first-lot'          => 'Primeiro número de lote',
                            'quantity-per-lot'   => 'Quantidade por lote',
                            'quantity-received'  => 'Quantidade recebida',
                            'keep-current-lines' => 'Manter linhas atuais',
                            'serials'            => 'Números de lote/série',
                            'serials-helper'     => 'Um número de lote/série por linha.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Adicional',

                'fields' => [
                    'responsible'                  => 'Responsável',
                    'shipping-policy'              => 'Política de envio',
                    'shipping-policy-hint-tooltip' => 'Define se os itens devem ser entregues parcialmente ou todos de uma vez.',
                    'scheduled-at'                 => 'Agendado em',
                    'scheduled-at-hint-tooltip'    => 'O horário programado para processar a primeira parte do envio. Definir um valor manualmente aqui aplicará esse valor como a data prevista para todas as movimentações de estoque.',
                    'source-document'              => 'Documento de origem',
                    'source-document-hint-tooltip' => 'Referência do documento',
                ],
            ],

            'note' => [
                'title' => 'Nota',

                'fields' => [

                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'        => 'Favorito',
            'reference'       => 'Referência',
            'from'            => 'De',
            'to'              => 'Para',
            'contact'         => 'Contato',
            'responsible'     => 'Responsável',
            'scheduled-at'    => 'Agendado em',
            'deadline'        => 'Prazo final',
            'closed-at'       => 'Fechado em',
            'source-document' => 'Documento de origem',
            'operation-type'  => 'Tipo de operação',
            'company'         => 'Empresa',
            'state'           => 'Estado',
            'deleted-at'      => 'Excluído em',
            'created-at'      => 'Criado em',
            'updated-at'      => 'Atualizado em',
        ],

        'groups' => [
            'state'           => 'Estado',
            'source-document' => 'Documento de origem',
            'operation-type'  => 'Tipo de operação',
            'scheduled-at'    => 'Programado para',
            'created-at'      => 'Criado em',
        ],

        'filters' => [
            'name'                 => 'Nome',
            'state'                => 'Estado',
            'partner'              => 'Parceiro',
            'responsible'          => 'Responsável',
            'owner'                => 'Proprietário',
            'source-location'      => 'Local de origem',
            'destination-location' => 'Local de destino',
            'deadline'             => 'Prazo final',
            'scheduled-at'         => 'Agendado em',
            'closed-at'            => 'Fechado em',
            'created-at'           => 'Criado em',
            'updated-at'           => 'Atualizado em',
            'company'              => 'Empresa',
            'creator'              => 'Criador',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Informações gerais',
                'entries' => [
                    'contact'              => 'Contato',
                    'operation-type'       => 'Tipo de operação',
                    'source-location'      => 'Local de origem',
                    'destination-location' => 'Local de destino',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title'   => 'Operações',
                'entries' => [
                    'product'        => 'Produto',
                    'final-location' => 'Local final',
                    'description'    => 'Descrição',
                    'scheduled-at'   => 'Agendado em',
                    'deadline'       => 'Prazo final',
                    'packaging'      => 'Embalagem',
                    'demand'         => 'Demanda',
                    'quantity'       => 'Quantidade',
                    'unit'           => 'Unidade',
                    'picked'         => 'Separado',
                ],
            ],
            'additional' => [
                'title'   => 'Informações adicionais',
                'entries' => [
                    'responsible'     => 'Responsável',
                    'shipping-policy' => 'Política de envio',
                    'scheduled-at'    => 'Agendado em',
                    'source-document' => 'Documento de origem',
                ],
            ],
            'note' => [
                'title' => 'Nota',
            ],
        ],
    ],

    'tabs' => [
        'todo'        => 'A fazer',
        'my'          => 'Minhas transferências',
        'starred'     => 'Favoritos',
        'draft'       => 'Rascunho',
        'waiting'     => 'Aguardando',
        'ready'       => 'Pronto',
        'done'        => 'Concluído',
        'canceled'    => 'Cancelado',
        'back-orders' => 'Pedidos pendentes',
    ],

    'notifications' => [
        'uom-precision-warning' => [
            'title' => 'Aviso de precisão da unidade de medida',
            'body'  => 'Você está usando uma unidade de medida menor que a usada para estocar este produto. Isso pode causar problemas de arredondamento nas quantidades reservadas. Considere usar a menor unidade de medida para a avaliação do estoque ou reduzir a precisão de arredondamento da sua unidade base.',
        ],
    ],
];
