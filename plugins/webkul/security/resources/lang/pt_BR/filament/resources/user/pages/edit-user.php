<?php

return [
    'notification' => [
        'title' => 'Usuário atualizado',
        'body' => 'O usuário foi atualizado com sucesso.',
    ],

    'header-actions' => [
        'change-password' => [
            'label' => 'Alterar senha',

            'notification' => [
                'title' => 'Senha alterada',
                'body' => 'A senha foi alterada com sucesso.',
            ],

            'form' => [
                'new-password' => 'Nova senha',
                'confirm-new-password' => 'Confirmar nova senha',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Usuário excluído',
                'body' => 'O usuário foi excluído com sucesso.',
                'error' => [
                    'title' => 'Usuário não pode ser excluído',
                    'body' => 'Este é um usuário padrão ou você não pode excluir a si mesmo.',
                ],
            ],
        ],
    ],
];
