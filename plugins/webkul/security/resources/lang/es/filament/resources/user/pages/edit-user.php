<?php

return [
    'notification' => [
        'title' => 'Usuario actualizado',
        'body'  => 'El usuario ha sido actualizado exitosamente.',
    ],

    'header-actions' => [
        'change-password' => [
            'label' => 'Cambiar Contraseña',

            'notification' => [
                'title' => 'Contraseña cambiada',
                'body'  => 'La contraseña ha sido cambiada exitosamente.',
            ],

            'form' => [
                'new-password'         => 'Nueva Contraseña',
                'confirm-new-password' => 'Confirmar Nueva Contraseña',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Usuario eliminado',
                'body'  => 'El usuario ha sido eliminado exitosamente.',
            ],
        ],
    ],
];
