<?php

return [
    'label'        => 'Enviar por Email',
    'resend-label' => 'Reenviar por Email',

    'form' => [
        'fields' => [
            'to'      => 'Para',
            'subject' => 'Asunto',
            'message' => 'Mensaje',
        ],
    ],

    'action' => [
        'notification' => [
            'success' => [
                'title' => 'Email enviado',
                'body'  => 'El email ha sido enviado exitosamente.',
            ],
        ],
    ],
];
