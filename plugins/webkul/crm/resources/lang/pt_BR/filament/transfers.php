<?php

return [
    'actions' => [
        'export' => [
            'label' => 'Exportar',
        ],

        'import' => [
            'label' => 'Importar',
        ],
    ],

    'columns' => [
        'id'                 => 'ID',
        'name'               => 'Nome',
        'contact-name'       => 'Nome do contato',
        'company-name'       => 'Nome da empresa',
        'job-title'          => 'Cargo',
        'email'              => 'E-mail',
        'phone'              => 'Telefone',
        'mobile'             => 'Celular',
        'website'            => 'Site',
        'street1'            => 'Rua',
        'street2'            => 'Rua 2',
        'city'               => 'Cidade',
        'zip'                => 'CEP',
        'country'            => 'País',
        'stage'              => 'Etapa',
        'salesperson'        => 'Vendedor',
        'team'               => 'Equipe de vendas',
        'priority'           => 'Prioridade',
        'probability'        => 'Probabilidade',
        'tags'               => 'Tags',
        'source'             => 'Origem',
        'medium'             => 'Mídia',
        'campaign'           => 'Campanha',
        'notes'              => 'Notas',
        'lost-reason'        => 'Motivo de perda',
        'created-at'         => 'Criado em',
        'customer'           => 'Cliente',
        'expected-revenue'   => 'Receita esperada',
        'prorated-revenue'   => 'Receita ponderada',
        'expected-closing'   => 'Fechamento previsto',
        'number'             => 'Número',
        'order-date'         => 'Data do pedido',
        'expiration'         => 'Validade',
        'delivery-date'      => 'Data de entrega',
        'customer-reference' => 'Referência do cliente',
        'source-document'    => 'Documento de origem',
        'untaxed-amount'     => 'Valor sem impostos',
        'taxes'              => 'Impostos',
        'total'              => 'Total',
        'currency'           => 'Moeda',
        'status'             => 'Status',
        'invoice-status'     => 'Status de faturamento',
    ],

    'notifications' => [
        'export' => [
            'completed' => 'Sua exportação está pronta: :count linhas exportadas.',
            'failed'    => ':count linhas não puderam ser exportadas.',
        ],

        'import' => [
            'completed' => 'Sua importação foi concluída: :count linhas importadas.',
            'failed'    => ':count linhas falharam; baixe-as nesta notificação para ver o motivo.',
        ],
    ],

    'errors' => [
        'record-not-found' => 'Nenhum registro com ID :id foi encontrado nas suas empresas.',
        'cannot-update'    => 'Você não tem permissão para atualizar o registro :id.',
        'cannot-create'    => 'Você não tem permissão para criar registros.',
        'not-found'        => ':field ":value" não foi encontrado.',
    ],
];
