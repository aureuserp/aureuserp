    <?php

    return [
        'setup' => [
            'title'               => 'Seguidores',
            'submit-action-title' => 'Añadir seguidor',
            'tooltip'             => 'Añadir seguidor',

            'form' => [
                'fields' => [
                    'recipients'  => 'Destinatarios',
                    'notify-user' => 'Notificar al usuario',
                    'add-a-note'  => 'Añadir una nota',
                ],
            ],

            'actions' => [
                'notification' => [
                    'success' => [
                        'title' => 'Seguidor añadido',
                        'body'  => 'El seguidor se ha añadido correctamente.',
                    ],

                    'partial_message' => [
                        'title'    => 'Mensaje enviado con un aviso',
                        'single'   => 'No se notificó a :count seguidor por falta de correo electrónico: :names',
                        'multiple' => 'No se notificó a :count seguidores por falta de correos electrónicos: :names',
                    ],

                    'error' => [
                        'title' => 'Error al añadir el seguidor',
                        'body'  => 'No se pudo añadir ":partner" como seguidor',
                    ],
                ],

                'mail' => [
                    'subject' => 'Invitación para seguir :model: :department',
                ],
            ],
        ],
    ];
