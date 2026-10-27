<?php

return [
    'navigation' => [
        'title' => 'Meus orçamentos',
    ],

    'pages' => [
        'list' => [
            'title' => 'Meus orçamentos',
        ],
    ],

    'views' => [
        'list'   => 'Lista',
        'kanban' => 'Kanban',
        'graph'  => 'Gráfico',
    ],

    'tabs' => [
        'opportunity' => 'Oportunidade',
    ],

    'board' => [
        'unauthorized' => 'Você não pode mover este orçamento para essa coluna.',

        'card' => [
            'open'        => 'Abrir :name',
            'customer'    => 'Cliente',
            'total'       => 'Total',
            'date'        => 'Data do pedido',
            'salesperson' => 'Vendedor',
        ],
    ],

    'actions' => [
        'group' => [
            'label' => 'Ações',
        ],

        'duplicate' => [
            'label'        => 'Duplicar',
            'notification' => ':count orçamento(s) duplicado(s).',
        ],

        'mark-as-sent' => [
            'label'        => 'Marcar orçamento como enviado',
            'notification' => ':count orçamento(s) marcado(s) como enviado(s).',
        ],

        'send-email' => [
            'label'        => 'Enviar e-mail',
            'description'  => 'Cada orçamento selecionado é enviado por e-mail ao cliente com o PDF anexado. Os rascunhos são marcados como enviados.',
            'submit'       => 'Enviar',
            'subject'      => 'Orçamento de :customer (Ref :name)',
            'body'         => 'Prezado(a) :customer,<br><br>Seu orçamento <strong>:name</strong> no valor de <strong>:amount</strong> está pronto para análise.<br><br>Se tiver alguma dúvida, entre em contato conosco.',
            'notification' => ':sent orçamento(s) enviado(s), :skipped ignorado(s) (não é orçamento, sem e-mail do cliente ou sem acesso).',
        ],

        'confirm' => [
            'label'        => 'Confirmar',
            'description'  => 'Os orçamentos selecionados se tornam pedidos de venda.',
            'notification' => ':confirmed orçamento(s) confirmado(s), :skipped ignorado(s).',
        ],
    ],

    'team-filter' => [
        'label' => 'Equipe de vendas: :team',
        'clear' => 'Mostrar tudo',
    ],
];
