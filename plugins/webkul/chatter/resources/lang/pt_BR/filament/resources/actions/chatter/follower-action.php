    <?php

    return [
        'setup' => [
            'title' => 'Seguidores',
            'submit-action-title' => 'Adicionar seguidor',
            'tooltip' => 'Adicionar seguidor',

            'form' => [
                'fields' => [
                    'recipients' => 'Destinatários',
                    'notify-user' => 'Notificar usuário',
                    'add-a-note' => 'Adicionar uma nota',
                ],
            ],

            'actions' => [
                'notification' => [
                    'success' => [
                        'title' => 'Seguidor adicionado',
                        'body' => 'O seguidor foi adicionado com sucesso.',
                    ],

                    'partial_message' => [
                        'title' => 'Mensagem enviada com aviso',
                        'single' => ':count seguidor não foi notificado devido à ausência de e-mail: :names',
                        'multiple' => ':count seguidores não foram notificados devido à ausência de e-mails: :names',
                    ],

                    'error' => [
                        'title' => 'Erro ao adicionar seguidor',
                        'body' => 'Falha ao adicionar ":partner" como seguidor',
                    ],
                ],

                'mail' => [
                    'subject' => 'Convite para seguir :model: :department',
                ],
            ],
        ],
    ];
