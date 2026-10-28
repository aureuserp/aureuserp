<?php

return [
    'navigation' => [
        'title' => 'Movimentações',
    ],

    'label'        => 'Movimentação de Ativo',
    'plural-label' => 'Movimentações de Ativos',

    'table' => [
        'columns' => [
            'occurred-at'        => 'Data e Hora',
            'asset-tag'          => 'Etiqueta do Ativo',
            'asset-name'         => 'Nome do Ativo',
            'type'               => 'Tipo de Movimentação',
            'source-type'        => 'Origem',
            'from'               => 'De',
            'to'                 => 'Para',
            'condition'          => 'Condição',
            'performed-by'       => 'Realizado por',
            'expected-return-at' => 'Retorno Esperado',
            'note'               => 'Nota',
            'attachments'        => 'Anexos',
        ],
        'groups' => [
            'date'         => 'Data',
            'type'         => 'Tipo de Movimentação',
            'asset'        => 'Ativo',
            'condition'    => 'Condição',
            'performed-by' => 'Realizado por',
        ],
        'filters' => [
            'type'         => 'Tipo de Movimentação',
            'source-type'  => 'Origem',
            'condition'    => 'Condição',
            'category'     => 'Categoria',
            'product'      => 'Produto',
            'asset'        => 'Ativo',
            'warehouse'    => 'Armazém',
            'employee'     => 'Funcionário',
            'department'   => 'Departamento',
            'site'         => 'Local',
            'performed-by' => 'Realizado por',
            'date-from'    => 'Data Inicial',
            'date-until'   => 'Data Final',
        ],
        'actions' => [
            'view' => 'Ver Detalhes',
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'Exportar',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'tabs' => [
                'all'           => 'Todos',
                'issues'        => 'Saídas',
                'returns'       => 'Devoluções',
                'transfers'     => 'Transferências',
                'scrapped-lost' => 'Descartado / Perdido',
                'damaged'       => 'Danificado / Ruim',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'details'     => 'Informações da Movimentação',
            'attachments' => 'Anexos e Documentação',
        ],
    ],
];
