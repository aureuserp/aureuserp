<?php

return [
    'label'        => 'Enviar por correo electrónico',
    'resend-label' => 'Reenviar por correo electrónico',

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
                'title' => 'Correo electrónico enviado',
                'body'  => 'El correo electrónico se ha enviado correctamente.',
            ],
        ],
    ],
];
