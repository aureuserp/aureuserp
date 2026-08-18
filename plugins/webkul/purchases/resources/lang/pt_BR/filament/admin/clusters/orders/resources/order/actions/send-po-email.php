<?php

return [
    'label' => 'Enviar pedido de compra por e-mail',

    'form' => [
        'fields' => [
            'to'      => 'Para',
            'subject' => 'Assunto',
            'message' => 'Mensagem',
        ],
    ],

    'action' => [
        'notification' => [
            'success' => [
                'title' => 'E-mail enviado',
                'body'  => 'O e-mail foi enviado com sucesso.',
            ],

            'warning' => [
                'title' => 'Alguns e-mails não foram enviados',
                'body'  => 'Endereço de e-mail ausente para :vendors.',
            ],
        ],
    ],
];
