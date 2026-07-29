<?php

return [
    'title'         => 'Restablecer contraseña',
    'heading'       => 'Restablecer contraseña',
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
        'password' => [
            'label'                => 'Nueva contraseña',
            'validation_attribute' => 'contraseña',
        ],
        'password_confirmation' => [
            'label' => 'Confirmar nueva contraseña',
        ],
        'actions' => [
            'reset' => [
                'label' => 'Restablecer contraseña',
            ],
        ],
    ],
];
