<?php

return [
    'breadcrumb' => 'Gestionar Usuarios',
    'title'      => 'Gestionar Usuarios',
    'group'      => 'General',

    'navigation' => [
        'label' => 'Gestionar Usuarios',
    ],

    'form' => [
        'enable-user-invitation' => [
            'label'       => 'Habilitar Invitación de Usuarios',
            'helper-text' => 'Permite a los usuarios invitar a otros usuarios a la aplicación.',
        ],

        'enable-reset-password' => [
            'label'       => 'Habilitar Restablecimiento de Contraseña',
            'helper-text' => 'Permite a los usuarios restablecer su contraseña.',
        ],

        'default-role' => [
            'label'       => 'Rol por Defecto',
            'helper-text' => 'El rol asignado por defecto a los nuevos usuarios.',
        ],

        'default-company' => [
            'label'       => 'Empresa por Defecto',
            'helper-text' => 'La empresa asignada por defecto a los nuevos usuarios.',
        ],
    ],
];
