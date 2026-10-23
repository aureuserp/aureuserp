<?php

return [
    'model-label'        => 'E-mail recebido',
    'plural-model-label' => 'E-mails recebidos',

    'navigation' => [
        'title' => 'E-mails recebidos',
    ],

    'table' => [
        'columns' => [
            'received-at' => 'Recebido',
            'from'        => 'De',
            'subject'     => 'Assunto',
            'status'      => 'Resultado',
            'team'        => 'Equipe de vendas',
            'lead'        => 'Lead / Oportunidade',
            'reason'      => 'Motivo',
            'recipients'  => 'Destinatários',
            'server'      => 'Servidor de e-mail',
        ],

        'empty' => [
            'heading'     => 'Nenhum e-mail recebido ainda',
            'description' => 'Os e-mails lidos dos servidores de e-mail de entrada aparecem aqui com o que foi feito com cada um.',
        ],
    ],
];
