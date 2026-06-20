<?php

return [
    'title' => 'Redefinir senha',
    'heading' => 'Redefinir senha',
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
            'label' => 'Nova senha',
            'validation_attribute' => 'senha',
        ],
        'password_confirmation' => [
            'label' => 'Confirmar nova senha',
        ],
        'actions' => [
            'reset' => [
                'label' => 'Redefinir senha',
            ],
        ],
    ],
];
