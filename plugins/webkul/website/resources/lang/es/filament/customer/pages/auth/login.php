<?php

return [
    'title'    => 'Iniciar sesión',
    'heading'  => 'Iniciar sesión',
    'messages' => [
        'failed' => 'Estas credenciales no coinciden con nuestros registros.',
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'Demasiados intentos. Inténtalo de nuevo en :seconds segundos.',
            'body'  => 'Espera :seconds segundos (:minutes minutos) antes de volver a intentarlo.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Correo electrónico',
        ],
        'password' => [
            'label' => 'Contraseña',
        ],
        'remember' => [
            'label' => 'Recordarme',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Iniciar sesión',
            ],
        ],
    ],
    'actions' => [
        'register' => [
            'before' => '¿No tienes una cuenta?',
            'label'  => 'Crear cuenta',
        ],
        'request_password_reset' => [
            'label' => '¿Olvidaste tu contraseña?',
        ],
    ],
];
