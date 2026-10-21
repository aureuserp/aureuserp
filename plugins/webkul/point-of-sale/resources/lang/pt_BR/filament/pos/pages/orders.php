<?php

return [
    'title' => 'Pedidos',

    'navigation' => [
        'label' => 'Pedidos',
    ],

    'walk-in' => 'Cliente avulso',

    'search' => 'Buscar por pedido, recibo ou cliente',

    'search-restaurant' => 'Buscar por pedido, recibo, cliente ou mesa',

    'select-order' => 'Selecione um pedido para ver suas linhas.',

    'line-discount' => '−:discount% de desconto',

    'taxes' => 'Impostos',

    'total' => 'Total',

    'status' => [
        'active' => 'Todos os pedidos ativos',
    ],

    'columns' => [
        'date'     => 'Data',
        'receipt'  => 'Número do recibo',
        'order'    => 'Número do pedido',
        'customer' => 'Cliente',
        'cashier'  => 'Operador de caixa',
        'tracking' => 'Número de rastreamento',
        'table'    => 'Mesa',
        'total'    => 'Total',
        'status'   => 'Status',
    ],

    'refund' => [
        'prompt'    => 'Selecione o(s) produto(s) a reembolsar e informe a quantidade',
        'refunded'  => 'Reembolsado:',
        'to-refund' => 'A reembolsar:',
        'qty'       => 'Qtd.',
        'price'     => 'Preço',
        'backspace' => 'Retrocesso',

        'max-exceeded' => [
            'title' => 'Máximo excedido',
            'body'  => 'A quantidade a reembolsar é maior que a quantidade pedida. Foram solicitados :requested, mas apenas :max pode ser reembolsado.',
        ],
    ],

    'actions' => [
        'print'    => 'Imprimir recibo',
        'back'     => 'Voltar',
        'details'  => 'Detalhes',
        'refund'   => 'Reembolso',
        'previous' => 'Página anterior',
        'next'     => 'Próxima página',

        'refund-notification' => [
            'title' => 'Reembolso criado',
            'body'  => 'O reembolso :order está pronto para ser liquidado.',
        ],

        'cancel' => [
            'label'       => 'Cancelar',
            'heading'     => 'Cancelar este pedido?',
            'description' => 'O pedido é encerrado sem pagamento e deixa de bloquear o fechamento do caixa. Esta ação não pode ser desfeita.',

            'notification' => [
                'title' => 'Pedido cancelado',
                'body'  => ':order foi cancelado.',
            ],
        ],

        'invoice' => [
            'label'   => 'Fatura',
            'heading' => 'Criar uma fatura para este pedido?',

            'notification' => [
                'title' => 'Fatura criada',
            ],
        ],
    ],

    'empty' => [
        'heading'     => 'Ainda não há pedidos',
        'description' => 'Os pedidos registrados durante a sessão aberta aparecerão aqui.',
    ],
];
