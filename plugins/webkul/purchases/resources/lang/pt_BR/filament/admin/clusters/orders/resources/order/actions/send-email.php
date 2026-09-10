<?php

return [
    'label'        => 'Enviar por e-mail',
    'resend-label' => 'Reenviar por e-mail',

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
                'body'  => 'Alguns fornecedores não receberão o e-mail porque o endereço de e-mail deles não está disponível.',
            ],

            'danger' => [
                'title' => 'E-mail não enviado',
                'body'  => 'Adicione um endereço de e-mail aos fornecedores selecionados e tente novamente.',
            ],
        ],
    ],
];
