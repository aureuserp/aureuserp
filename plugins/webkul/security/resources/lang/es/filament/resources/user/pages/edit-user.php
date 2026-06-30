<?php

return [
    'notification' => [
        'title' => 'Usuario actualizado',
        'body'  => 'El usuario se ha actualizado correctamente.',
    ],

    'header-actions' => [
        'change-password' => [
            'label' => 'Cambiar contraseña',

            'notification' => [
                'title' => 'Contraseña cambiada',
                'body'  => 'La contraseña se ha cambiado correctamente.',
            ],

            'form' => [
                'new-password'         => 'Nueva contraseña',
                'confirm-new-password' => 'Confirmar nueva contraseña',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Usuario eliminado',
                'body'  => 'El usuario se ha eliminado correctamente.',
                'error' => [
                    'title' => 'No se puede eliminar el usuario',
                    'body'  => 'Este es un usuario predeterminado o no puede eliminarse a sí mismo.',
                ],
            ],
        ],
    ],
];
