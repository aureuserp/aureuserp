<?php

return [
    'mark-lost' => [
        'label'        => 'Marcar como perdido',
        'notification' => ':count registro(s) marcado(s) como perdido(s).',
    ],

    'send-email' => [
        'label'        => 'Enviar correo',
        'short-label'  => 'Correo',
        'submit'       => 'Enviar',

        'form' => [
            'recipient' => 'Para',
            'subject'   => 'Asunto',
            'body'      => 'Mensaje',
        ],

        'sent'         => 'Correo enviado a :email.',
        'failed'       => 'No se pudo enviar el correo.',
        'notification' => ':sent correo(s) enviado(s), :skipped registro(s) omitido(s) (sin correo o sin acceso).',
    ],

    'send-sms' => [
        'label'        => 'Enviar SMS',
        'short-label'  => 'SMS',
        'submit'       => 'Enviar SMS',

        'form' => [
            'recipient' => 'Destinatario',
            'message'   => 'Mensaje',
        ],

        'log-subject'  => 'SMS a :number',
        'sent'         => 'SMS enviado a :number.',
        'failed'       => 'No se pudo enviar el SMS.',
        'notification' => ':sent SMS enviado(s), :skipped registro(s) omitido(s) (sin número válido o sin acceso).',
    ],
];
