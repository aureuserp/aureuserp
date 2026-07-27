<?php

return [
    'title' => 'Cancelar',
    'modal' => [
        'heading'     => 'Cancelar orçamento',
        'description' => 'Tem certeza de que deseja cancelar esta orçamento?',
    ],

    'footer-actions' => [
        'send-and-cancel' => [
            'title' => 'Enviar e cancelar',

            'notification' => [
                'cancelled' => [
                    'title' => 'Orçamento cancelada',
                    'body'  => 'A orçamento foi cancelada e o e-mail foi enviado com sucesso.',
                ],
            ],
        ],

        'cancel' => [
            'title' => 'Cancelar',

            'notification' => [
                'cancelled' => [
                    'title' => 'Orçamento cancelada',
                    'body'  => 'A orçamento foi cancelada com sucesso.',
                ],
            ],
        ],

        'close' => [
            'title' => 'Fechar',
        ],
    ],

    'form' => [
        'fields' => [
            'partner'             => 'Parceiro',
            'subject'             => 'Assunto',
            'subject-placeholder' => 'Assunto',
            'subject-default'     => 'A orçamento :name foi cancelada para o pedido de venda nº :id',
            'description'         => 'Descrição',
            'description-default' => 'Prezado(a) <b>:partner_name</b>, <br/><br/>Gostaríamos de informar que seu pedido de venda <b>:name</b> foi cancelado. Como resultado, nenhuma cobrança adicional será aplicada a este pedido. Se um reembolso for necessário, ele será processado o mais breve possível.<br/><br/>Caso tenha alguma dúvida ou precise de mais assistência, fique à vontade para entrar em contato conosco.',
        ],
    ],
];
