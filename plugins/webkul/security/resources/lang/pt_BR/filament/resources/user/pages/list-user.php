<?php

return [
    'tabs' => [
        'all' => 'Todos os usuários',
        'archived' => 'Usuários arquivados',
    ],

    'header-actions' => [
        'invite' => [
            'title' => 'Convidar usuário',
            'modal' => [
                'submit-action-label' => 'Convidar usuário',
            ],
            'form' => [
                'email' => 'E-mail',
            ],
            'notification' => [
                'success' => [
                    'title' => 'Usuário convidado',
                    'body' => 'Usuário convidado com sucesso',
                ],
                'error' => [
                    'title' => 'Falha no convite do usuário',
                    'body' => 'O sistema encontrou um erro inesperado ao tentar enviar o convite do usuário.',
                ],

                'default-company-error' => [
                    'title' => 'Empresa padrão não definida',
                    'body' => 'Defina a empresa padrão nas configurações antes de convidar um usuário.',
                ],
            ],
        ],

        'create' => [
            'label' => 'Novo usuário',
        ],
    ],
];
