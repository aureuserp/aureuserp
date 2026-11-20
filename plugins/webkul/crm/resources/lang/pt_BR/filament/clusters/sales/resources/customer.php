<?php

return [
    'model-label'        => 'Cliente',
    'plural-model-label' => 'Clientes',

    'navigation' => [
        'title' => 'Clientes',
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nome',
            'phone'       => 'Telefone',
            'email'       => 'E-mail',
            'salesperson' => 'Vendedor',
            'city'        => 'Cidade',
            'country'     => 'País',
            'company'     => 'Empresa',
        ],
    ],

    'grid' => [
        'opportunities' => '{1} :count oportunidade|[0,*] :count oportunidades',
        'sale-orders'   => '{1} :count pedido de venda|[0,*] :count pedidos de venda',
    ],

    'board' => [
        'unauthorized' => 'Este cliente não pode ser movido para essa coluna.',
    ],

    'actions' => [
        'merge' => [
            'label'              => 'Mesclar',
            'heading'            => 'Mesclar clientes',
            'description'        => 'Tudo o que está ligado aos outros clientes (oportunidades, pedidos, contatos filhos, mensagens…) passa para o cliente mantido. Os campos vazios dele são preenchidos com os dos outros, e os outros são arquivados.',
            'submit'             => 'Mesclar clientes',
            'destination'        => 'Cliente a manter',
            'destination-helper' => 'Os outros clientes selecionados são mesclados neste.',
            'invalid'            => 'Selecione pelo menos dois clientes que você pode editar e arquivar.',
            'notification'       => ':count cliente(s) mesclado(s) em :name.',
            'log'                => 'Clientes mesclados: :names',
        ],

        'vcard' => [
            'label' => 'Baixar (vCard)',
        ],

        'portal' => [
            'label'        => 'Conceder acesso ao portal',
            'description'  => 'Cada cliente selecionado com e-mail recebe um convite para definir uma senha do portal.',
            'notification' => 'Acesso ao portal concedido a :granted cliente(s), :skipped ignorado(s) (sem e-mail, já tem acesso, e-mail em uso ou sem permissão).',
        ],
    ],

    'infolist' => [
        'sections' => [
            'overview' => 'Visão geral',
        ],

        'fields' => [
            'opportunities' => 'Oportunidades',
            'sale-orders'   => 'Pedidos de venda',
        ],
    ],
];
