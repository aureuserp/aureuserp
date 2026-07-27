<?php

return [
    'label' => 'Enviar por e-mail',
    'resend-label' => 'Reenviar por e-mail',

    'form' => [
        'fields' => [
            'to' => 'Para',
            'subject' => 'Assunto',
            'message' => 'Mensagem',
        ],
    ],

    'action' => [
        'notification' => [
            'success' => [
                'title' => 'E-mail enviado',
                'body' => 'O e-mail foi enviado com sucesso.',
            ],
        ],
    ],
];
