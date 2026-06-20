<?php

return [
    'title' => 'Esqueci a senha',
    'heading' => 'Esqueci a senha',
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
        'actions' => [
            'request' => [
                'label' => 'Enviar link de redefinição',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'label' => 'Voltar para o login',
        ],
    ],
];
