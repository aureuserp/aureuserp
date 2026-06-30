<?php

return [
    'breadcrumb' => 'Gestionar usuarios',
    'title'      => 'Gestionar usuarios',
    'group'      => 'General',

    'navigation' => [
        'label' => 'Gestionar usuarios',
    ],

    'form' => [
        'enable-user-invitation' => [
            'label'       => 'Habilitar invitación de usuarios',
            'helper-text' => 'Permitir que los usuarios inviten a otros usuarios a la aplicación.',
        ],

        'enable-reset-password' => [
            'label'       => 'Habilitar restablecimiento de contraseña',
            'helper-text' => 'Permitir que los usuarios restablezcan su contraseña.',
        ],

        'default-role' => [
            'label'       => 'Rol predeterminado',
            'helper-text' => 'El rol predeterminado asignado a los nuevos usuarios.',
        ],

        'default-company' => [
            'label'       => 'Empresa predeterminada',
            'helper-text' => 'La empresa predeterminada asignada a los nuevos usuarios.',
        ],
    ],
];
