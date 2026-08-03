<?php

return [
    'group' => [
        'label' => 'Acesso ao Portal',
    ],

    'grant' => [
        'label' => 'Conceder Acesso ao Portal',

        'modal' => [
            'heading'     => 'Conceder Acesso ao Portal',
            'description' => 'Um e-mail de convite será enviado para :email com um link para definir uma senha para o portal do cliente.',
        ],

        'notification' => [
            'email-missing' => [
                'title' => 'Endereço de e-mail obrigatório',
                'body'  => 'Adicione um endereço de e-mail a este contato antes de conceder acesso ao portal.',
            ],

            'email-taken' => [
                'title' => 'Endereço de e-mail já está em uso',
                'body'  => 'Outro contato com acesso ao portal já está usando este endereço de e-mail.',
            ],

            'granted' => [
                'title' => 'Acesso ao portal concedido',
                'body'  => 'Um convite para definir uma senha foi enviado para :email.',
            ],

            'email-failed' => [
                'title' => 'Acesso ao portal concedido, mas o e-mail de convite não pôde ser enviado',
            ],
        ],
    ],

    'change-password' => [
        'label' => 'Alterar Senha',

        'modal' => [
            'heading'     => 'Alterar Senha do Portal',
            'description' => 'Defina uma nova senha do portal do cliente para :email.',
        ],

        'form' => [
            'password' => [
                'label' => 'Nova Senha',
            ],

            'password-confirmation' => [
                'label' => 'Confirmar Nova Senha',
            ],
        ],

        'notification' => [
            'changed' => [
                'title' => 'Senha do portal alterada',
                'body'  => 'O contato agora pode entrar no portal do cliente com a nova senha.',
            ],
        ],
    ],

    'password-reset' => [
        'label' => 'Enviar Redefinição de Senha',

        'modal' => [
            'heading'     => 'Enviar Redefinição de Senha',
            'description' => 'Um link de redefinição de senha será enviado para :email.',
        ],

        'notification' => [
            'sent' => [
                'title' => 'Link de redefinição de senha enviado',
                'body'  => 'Um link de redefinição de senha foi enviado para :email.',
            ],

            'failed' => [
                'title' => 'O link de redefinição de senha não pôde ser enviado',
            ],
        ],
    ],

    'revoke' => [
        'label' => 'Revogar Acesso ao Portal',

        'modal' => [
            'heading'     => 'Revogar Acesso ao Portal',
            'description' => 'O contato não poderá mais entrar no portal do cliente. Quaisquer links pendentes de redefinição de senha serão invalidados.',
        ],

        'notification' => [
            'revoked' => [
                'title' => 'Acesso ao portal revogado',
                'body'  => 'O contato não pode mais entrar no portal do cliente.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'portal-access' => 'Acesso ao Portal',
        ],

        'filters' => [
            'portal-access' => 'Acesso ao Portal',
        ],
    ],

    'infolist' => [
        'section' => [
            'title' => 'Portal do Cliente',
        ],

        'entries' => [
            'status' => [
                'label'   => 'Acesso ao Portal',
                'granted' => 'Concedido',
                'none'    => 'Não Concedido',
            ],

            'email-verified-at' => [
                'label'       => 'E-mail Verificado Em',
                'placeholder' => 'Nunca',
            ],

            'last-login-at' => [
                'label'       => 'Último Login no Portal',
                'placeholder' => 'Nunca',
            ],
        ],
    ],
];
