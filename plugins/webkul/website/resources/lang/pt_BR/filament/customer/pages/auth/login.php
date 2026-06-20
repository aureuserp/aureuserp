<?php

return [
    'title' => 'Entrar',
    'heading' => 'Entrar',
    'messages' => [
        'failed' => 'Essas credenciais não correspondem aos nossos registros.',
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'Muitas tentativas. Tente novamente em :seconds segundos.',
            'body' => 'Aguarde :seconds segundos (:minutes minutos) antes de tentar novamente.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Endereço de e-mail',
        ],
        'password' => [
            'label' => 'Senha',
        ],
        'remember' => [
            'label' => 'Lembrar de mim',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Entrar',
            ],
        ],
    ],
    'actions' => [
        'register' => [
            'before' => 'Não tem uma conta?',
            'label' => 'Criar conta',
        ],
        'request_password_reset' => [
            'label' => 'Esqueceu a senha?',
        ],
    ],
];
