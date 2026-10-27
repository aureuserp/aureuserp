<?php

return [
    'model-label'        => 'Contato',
    'plural-model-label' => 'Contatos',

    'navigation' => [
        'title' => 'Contatos',
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
        'unauthorized' => 'Este contato não pode ser movido para essa coluna.',
    ],

    'actions' => [
        'merge' => [
            'label'              => 'Mesclar',
            'heading'            => 'Mesclar contatos',
            'description'        => 'Tudo o que está ligado aos outros contatos (oportunidades, pedidos, contatos filhos, mensagens…) passa para o contato mantido. Os campos vazios dele são preenchidos com os dos outros, e os outros são arquivados.',
            'submit'             => 'Mesclar contatos',
            'destination'        => 'Contato a manter',
            'destination-helper' => 'Os outros contatos selecionados são mesclados neste.',
            'invalid'            => 'Selecione pelo menos dois contatos que você pode editar e arquivar.',
            'notification'       => ':count contato(s) mesclado(s) em :name.',
            'log'                => 'Contatos mesclados: :names',
        ],

        'vcard' => [
            'label' => 'Baixar (vCard)',
        ],

        'portal' => [
            'label'        => 'Conceder acesso ao portal',
            'description'  => 'Cada contato selecionado com e-mail recebe um convite para definir uma senha do portal.',
            'notification' => 'Acesso ao portal concedido a :granted contato(s), :skipped ignorado(s) (sem e-mail, já tem acesso, e-mail em uso ou sem permissão).',
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
