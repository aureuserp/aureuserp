<?php

return [
    'title' => 'Cadastrar',
    'heading' => 'Cadastrar',
    'notifications' => [
        'throttled' => [
            'title' => 'Muitas tentativas. Tente novamente em :seconds segundos.',
            'body' => 'Aguarde :seconds segundos (:minutes minutos) antes de tentar novamente.',
        ],
    ],
    'form' => [
        'name' => [
            'label' => 'Nome',
        ],
        'email' => [
            'label' => 'Endereço de e-mail',
        ],
        'password' => [
            'label' => 'Senha',
            'validation_attribute' => 'senha',
        ],
        'password_confirmation' => [
            'label' => 'Confirmar senha',
        ],
        'actions' => [
            'register' => [
                'label' => 'Criar conta',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'before' => 'Já tem uma conta?',
            'label' => 'Entrar',
        ],
    ],
];
