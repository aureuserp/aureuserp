<?php

return [
    'setup' => [
        'title'             => 'Seguidores',
        'submit-action-title' => 'Añadir Seguidor',
        'tooltip'           => 'Añadir Seguidor',

        'form' => [
            'fields' => [
                'recipients'  => 'Destinatarios',
                'notify-user' => 'Notificar Usuario',
                'add-a-note'  => 'Añadir una nota',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Seguidor Añadido',
                    'body'  => '":partner" ha sido añadido como seguidor.',
                ],

                'error' => [
                    'title' => 'Error al añadir seguidor',
                    'body'  => 'Error al añadir ":partner" como seguidor',
                ],
            ],

            'mail' => [
                'subject' => 'Invitación a seguir :model: :department',
            ],
        ],
    ],
];
