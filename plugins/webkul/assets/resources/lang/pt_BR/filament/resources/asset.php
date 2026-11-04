<?php

return [
    'title'        => 'Ativo da Empresa',
    'plural-title' => 'Ativos da Empresa',

    'navigation' => [
        'title' => 'Ativos',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Detalhes do Ativo',
                'fields' => [
                    'name'                       => 'Nome do Ativo',
                    'product'                    => 'Produto / Modelo',
                    'product-type'               => 'Tipo de Produto',
                    'quantity'                   => 'Quantidade',
                    'quantity-help'              => 'Quantidade total recebida neste lote.',
                    'asset-tag'                  => 'Código do Ativo',
                    'asset-tag-help'             => 'Código identificador único para o ativo dentro da empresa.',
                    'asset-tag-auto-placeholder' => 'AST-XXXXX (Gerado automaticamente)',
                    'serial-no'                  => 'Número de Série',
                    'model'                      => 'Modelo',
                    'category'                   => 'Categoria',
                    'condition'                  => 'Condição',
                    'cost'                       => 'Custo',
                    'purchase-date'              => 'Data de Compra',
                    'warranty-until'             => 'Garantia Até',
                    'warehouse'                  => 'Armazém',
                    'warehouse-help'             => 'Armazém de estoque onde este ativo fica armazenado quando disponível.',
                    'initial-placement'          => 'Alocação inicial',
                    'placements'                 => [
                        'warehouse'        => 'Armazém de inventário',
                        'site-asset'       => 'Ativo de local fixo',
                        'employee-custody' => 'Custódia do funcionário',
                    ],
                    'current-site'               => 'Local',
                    'current-employee'           => 'Funcionário',
                    'note'                       => 'Notas',
                ],
            ],

            'settings' => [
                'title'  => 'Configurações',
                'fields' => [
                    'company' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'asset-tag'        => 'Código do Ativo',
            'name'             => 'Nome do Ativo',
            'product'          => 'Produto',
            'product-type'     => 'Tipo de Produto',
            'quantity'         => 'Quantidade',
            'model'            => 'Modelo',
            'serial-no'        => 'Nº de Série',
            'category'         => 'Categoria',
            'status'           => 'Status',
            'condition'        => 'Condição',
            'holder'           => 'Custódia Atual',
            'warehouse'        => 'Armazém',
            'current-employee' => 'Funcionário Atual',
            'current-site'     => 'Local Atual',
            'cost'             => 'Custo',
            'company'          => 'Empresa',
            'deleted-at'       => 'Arquivado em',
            'created-at'       => 'Criado em',
            'updated-at'       => 'Atualizado em',
        ],

        'groups' => [
            'product'          => 'Produto / Modelo',
            'category'         => 'Categoria',
            'status'           => 'Status',
            'condition'        => 'Condição',
            'warehouse'        => 'Armazém',
            'current-employee' => 'Funcionário Atual',
            'current-site'     => 'Local Atual',
        ],

        'filters' => [
            'status'              => 'Status',
            'condition'           => 'Condição',
            'category'            => 'Categoria',
            'current-holder-type' => 'Tipo de Custódia',
            'current-employee'    => 'Funcionário Atual',
            'department'          => 'Departamento',
            'warehouse'           => 'Armazém',
            'current-site'        => 'Local Atual',
            'site-usage-type'     => 'Uso do local',
            'site-type'           => 'Tipo de local',
            'product'             => 'Produto',
            'product-type'        => 'Tipo de Produto',
            'company'             => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Ativo atualizado',
                    'body'  => 'O ativo foi atualizado com sucesso.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Não foi possível excluir o ativo',
                    ],
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'Exportar',
            ],
        ],
    ],

    'actions' => [
        'issue' => [
            'label' => 'Entregar Custódia',
            'modal' => [
                'heading'                => 'Entregar Custódia de Ativo',
                'description'            => 'Atribuir este ativo a um funcionário ou local.',
                'consumable-description' => 'Saldo disponível: :quantity unidades. As quantidades são deduzidas deste lote na entrega.',
                'submit'                 => 'Confirmar Entrega',
            ],
            'fields' => [
                'allocation-mode'    => 'Modo de alocação',
                'allocation-modes'   => [
                    'employee-custody' => 'Custódia do funcionário',
                    'site-asset'       => 'Ativo de local fixo',
                ],
                'holder-type'        => 'Tipo de Custódia',
                'employee'           => 'Funcionário',
                'employees'          => 'Destinatários',
                'departments'        => 'Departamentos',
                'departments-help'   => 'Seleção em massa de destinatários por departamento. Escolher um local ou departamentos preenche a lista automaticamente.',
                'recipients-summary' => ':count destinatário(s) — :total unidade(s) no total',
                'site'               => 'Local',
                'receiver'           => 'Recebedor em Campo',
                'condition-out'      => 'Condição de Saída',
                'quantity'           => 'Quantidade por Destinatário',
                'quantity-help'      => 'Cada destinatário selecionado recebe esta quantidade. O saldo do lote é reduzido na mesma medida.',
                'issued-at'          => 'Data e Hora de Entrega',
                'expected-return-at' => 'Retorno Esperado',
                'issue-note'         => 'Nota de Entrega',
                'issue-attachments'  => 'Anexos de entrega',
            ],
            'notification' => [
                'title' => 'Custódia Entregue',
                'body'  => 'A custódia do ativo foi entregue com sucesso.',
            ],
        ],

        'transfer' => [
            'label' => 'Transferir Custódia',
            'modal' => [
                'heading'     => 'Transferir Custódia de Ativo',
                'description' => 'Transferir este ativo para um novo funcionário ou local.',
                'submit'      => 'Confirmar Transferência',
            ],
            'fields' => [
                'allocation-mode'    => 'Modo de alocação',
                'allocation-modes'   => [
                    'employee-custody' => 'Custódia do funcionário',
                    'site-asset'       => 'Ativo de local fixo',
                ],
                'holder-type'        => 'Novo Tipo de Custódia',
                'employee'           => 'Novo Funcionário',
                'site'               => 'Novo Local',
                'receiver'           => 'Recebedor em Campo',
                'condition-in'       => 'Condição do Detentor Anterior',
                'issued-at'          => 'Data e Hora de Transferência',
                'expected-return-at' => 'Retorno Esperado',
                'issue-note'         => 'Nota de Transferência',
                'return-note'        => 'Nota de Devolução do Detentor Anterior',
                'return-attachments' => 'Anexos de devolução anterior',
                'issue-attachments'  => 'Anexos de nova entrega',
            ],
            'notification' => [
                'title' => 'Custódia Transferida',
                'body'  => 'A custódia do ativo foi transferida com sucesso.',
            ],
        ],

        'return' => [
            'label' => 'Devolver ao Estoque',
            'modal' => [
                'heading'     => 'Devolver Ativo ao Inventário',
                'description' => 'Receber e verificar o ativo de volta ao inventário disponível.',
                'submit'      => 'Confirmar Devolução',
            ],
            'fields' => [
                'condition-in'       => 'Condição de Retorno',
                'ended-at'           => 'Data e Hora de Devolução',
                'warehouse'          => 'Armazém de recebimento',
                'return-note'        => 'Nota de Devolução',
                'return-attachments' => 'Anexos de devolução',
            ],
            'notification' => [
                'title' => 'Ativo Devolvido',
                'body'  => 'O ativo foi devolvido ao inventário disponível.',
            ],
        ],

        'scrap' => [
            'label' => 'Sucatear Ativo',
            'modal' => [
                'heading'     => 'Sucatear Ativo',
                'description' => 'Marcar este ativo como sucateado. Isso encerra qualquer custódia aberta.',
                'submit'      => 'Confirmar Sucateamento',
            ],
            'fields' => [
                'ended-at'    => 'Data e Hora de Sucateamento',
                'note'        => 'Motivo / Nota de Sucateamento',
                'attachments' => 'Anexos de descarte',
            ],
            'notification' => [
                'title' => 'Ativo Sucateado',
                'body'  => 'O ativo foi marcado como sucateado.',
            ],
        ],

        'mark-lost' => [
            'label' => 'Marcar como Perdido',
            'modal' => [
                'heading'     => 'Marcar Ativo como Perdido',
                'description' => 'Marcar este ativo como perdido. Isso encerra qualquer custódia aberta.',
                'submit'      => 'Confirmar Perda',
            ],
            'fields' => [
                'ended-at'    => 'Data e Hora do Relato',
                'note'        => 'Detalhes da Perda',
                'attachments' => 'Anexos do relatório',
            ],
            'notification' => [
                'title' => 'Ativo Marcado como Perdido',
                'body'  => 'O ativo foi marcado como perdido.',
            ],
        ],

        'bulk-register' => [
            'label' => 'Registro em Massa de Unidades',
            'modal' => [
                'heading'     => 'Registrar Unidades de Ativos em Massa',
                'description' => 'Registrar múltiplas unidades do mesmo modelo em um único lote.',
                'submit'      => 'Registrar Unidades',
            ],
            'sections' => [
                'shared'  => 'Detalhes Comuns do Lote',
                'mode'    => 'Modo de Rastreamento e Entrada',
                'preview' => 'Visualização de Validação',
            ],
            'fields' => [
                'company'             => 'Empresa',
                'product'             => 'Produto / Modelo',
                'name'                => 'Nome do Ativo',
                'model'               => 'Modelo',
                'category'            => 'Categoria',
                'condition'           => 'Condição Inicial',
                'cost'                => 'Custo Unitário',
                'purchase-date'       => 'Data de Compra',
                'warranty-until'      => 'Garantia Até',
                'warehouse'           => 'Armazém',
                'warehouse-help'      => 'Armazém inicial para os ativos disponíveis registrados.',
                'initial-placement'   => 'Alocação inicial',
                'placements'          => [
                    'warehouse'        => 'Armazém de inventário',
                    'site-asset'       => 'Ativo de local fixo',
                    'employee-custody' => 'Custódia do funcionário',
                ],
                'site'                => 'Local',
                'employee'            => 'Funcionário',
                'attachments'         => 'Anexos de entrega',
                'note'                => 'Nota do Lote',
                'tracking-mode'       => 'Modo de Rastreamento',
                'modes'               => [
                    'with-serials'    => 'Unidades Serializadas (Rastreadas por Nº de Série)',
                    'without-serials' => 'Unidades Não Serializadas (Cadeiras, ferramentas, etc.)',
                ],
                'serials-raw'         => 'Lista de Séries (uma por linha ou separadas por vírgula)',
                'serials-raw-help'    => 'Cole as séries: Nº de série por linha ou TAG,N_SERIE.',
                'parse-to-table'      => 'Converter em tabela',
                'items'               => 'Entradas de unidades',
                'serial-no'           => 'Número de série',
                'asset-tag'           => 'Código do ativo',
                'quantity'            => 'Quantidade a Gerar',
                'tags-raw'            => 'Códigos de Ativos Pré-impressos',
                'tags-raw-help'       => 'Um código de ativo por linha para unidades não serializadas.',
                'unit-note'           => 'Nota da unidade',
            ],
            'preview' => [
                'valid'      => ':count Unidades Válidas',
                'duplicates' => ':count Duplicados na Lista',
                'collisions' => ':count Existentes no BD',
            ],
            'notification' => [
                'title' => 'Registro em Massa Concluído',
                'body'  => ':count unidades de ativos foram registradas com sucesso.',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'identity' => [
                'title'   => 'Identidade do Ativo',
                'entries' => [
                    'asset-tag' => 'Código do Ativo',
                    'name'      => 'Nome do Ativo',
                    'product'   => 'Produto',
                    'model'     => 'Modelo',
                    'category'  => 'Categoria',
                    'serial-no' => 'Número de Série',
                    'status'    => 'Status',
                    'condition' => 'Condição',
                ],
            ],

            'custody' => [
                'title'   => 'Custódia Atual',
                'entries' => [
                    'current-employee'   => 'Funcionário Atual',
                    'current-site'       => 'Local Atual',
                    'site-usage'         => 'Uso do Local',
                    'warehouse'          => 'Armazém',
                    'initial-quantity'   => 'Quantidade inicial',
                    'available-quantity' => 'Quantidade disponível',
                    'issued-at'          => 'Data de Entrega',
                    'expected-return-at' => 'Retorno Esperado',
                    'custody-note'       => 'Nota de Custódia',
                ],
            ],

            'acquisition' => [
                'title'   => 'Aquisição e Financeiro',
                'entries' => [
                    'company'        => 'Empresa',
                    'purchase-date'  => 'Data de Compra',
                    'warranty-until' => 'Garantia Até',
                    'cost'           => 'Custo',
                    'creator'        => 'Criado por',
                ],
            ],

            'notes' => [
                'title'   => 'Notas',
                'entries' => [
                    'note' => 'Notas',
                ],
            ],

            'audit' => [
                'title'   => 'Auditoria',
                'entries' => [
                    'created-at' => 'Criado em',
                    'updated-at' => 'Atualizado em',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'assignments' => [
            'title'   => 'Histórico de Custódia e Movimentações',
            'columns' => [
                'occurred-at'        => 'Data e Hora',
                'type'               => 'Tipo de Movimentação',
                'source-type'        => 'Origem',
                'from-display'       => 'De',
                'to-display'         => 'Para',
                'condition'          => 'Condição',
                'performed-by'       => 'Realizado por',
                'expected-return-at' => 'Retorno Esperado',
                'note'               => 'Nota',
                'attachments'        => 'Anexos',
            ],
            'filters' => [
                'type'        => 'Tipo de Movimentação',
                'source-type' => 'Origem',
            ],
            'view-modal' => [
                'heading'  => 'Detalhes da Movimentação',
                'sections' => [
                    'details'     => 'Informações da Movimentação',
                    'attachments' => 'Documentos e Fotos Anexados',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'Novo Ativo',
                    'notification' => [
                        'title' => 'Ativo criado',
                        'body'  => 'O ativo foi criado com sucesso.',
                    ],
                ],
            ],
            'tabs' => [
                'all'            => 'Todos',
                'available'      => 'Disponível',
                'consumables'    => 'Consumíveis',
                'site-assets'    => 'Ativos de local fixo',
                'with-employees' => 'Com Funcionários',
                'at-sites'       => 'Em Locais / Depósitos',
                'in-the-field'   => 'No Campo',
                'overdue-return' => 'Retorno Atrasado',
                'scrapped-lost'  => 'Sucateado / Perdido',
                'archived'       => 'Arquivado',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Ativo criado',
                'body'  => 'O ativo foi criado com sucesso.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Ativo atualizado',
                'body'  => 'O ativo foi atualizado com sucesso.',
            ],
        ],
    ],
];
