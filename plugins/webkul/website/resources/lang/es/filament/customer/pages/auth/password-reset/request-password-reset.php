<?php

return [
    'title'         => 'Olvidé mi contraseña',
    'heading'       => 'Olvidé mi contraseña',
    'notifications' => [
        'throttled' => [
            'title' => 'Demasiados intentos. Vuelva a intentarlo en :seconds segundos.',
            'body'  => 'Espere :seconds segundos (:minutes minutos) antes de volver a intentarlo.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Correo electrónico',
        ],
        'actions' => [
            'request' => [
                'label' => 'Enviar enlace de restablecimiento',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'label' => 'Volver a iniciar sesión',
        ],
    ],
];
