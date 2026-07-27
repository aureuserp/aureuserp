<?php

return [
    'title'         => 'Registrarse',
    'heading'       => 'Registrarse',
    'notifications' => [
        'throttled' => [
            'title' => 'Demasiados intentos. Vuelva a intentarlo en :seconds segundos.',
            'body'  => 'Espere :seconds segundos (:minutes minutos) antes de volver a intentarlo.',
        ],
    ],
    'form' => [
        'name' => [
            'label' => 'Nombre',
        ],
        'email' => [
            'label' => 'Correo electrónico',
        ],
        'password' => [
            'label'                => 'Contraseña',
            'validation_attribute' => 'contraseña',
        ],
        'password_confirmation' => [
            'label' => 'Confirmar contraseña',
        ],
        'actions' => [
            'register' => [
                'label' => 'Crear cuenta',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'before' => '¿Ya tiene una cuenta?',
            'label'  => 'Iniciar sesión',
        ],
    ],
];
