<?php

return [
    'title'                   => 'Perfil',
    'heading'                 => 'Perfil',
    'subheading'              => 'Gestionar la configuración y las preferencias de la cuenta.',
    'information_section'     => 'Información del perfil',
    'information_description' => 'Actualizar la información del perfil y el correo electrónico de la cuenta.',

    'notification' => [
        'success' => [
            'title' => 'Perfil actualizado',
            'body'  => 'El perfil se ha actualizado correctamente.',
        ],

        'error' => [
            'title' => 'Error al actualizar el perfil',
            'body'  => 'Se produjo un error al actualizar el perfil.',
        ],

        'validation-error' => [
            'title' => 'Error de validación',
        ],
    ],

    'actions' => [
        'save' => 'Guardar cambios',
    ],

    'fields' => [
        'avatar'          => 'Foto de perfil',
        'name'            => 'Nombre',
        'email'           => 'Correo electrónico',
        'language'        => 'Idioma preferido',
        'language_helper' => 'La interfaz de administración se mostrará en este idioma.',
    ],

    'password' => [
        'section'     => 'Actualizar contraseña',
        'description' => 'Asegúrese de que la cuenta use una contraseña larga y aleatoria para mayor seguridad.',
        'current'     => 'Contraseña actual',
        'new'         => 'Nueva contraseña',
        'confirm'     => 'Confirmar contraseña',
        'helper'      => 'Debe tener al menos 8 caracteres.',

        'errors' => [
            'current-required'  => 'La contraseña actual es obligatoria.',
            'current-incorrect' => 'La contraseña actual es incorrecta. Inténtelo de nuevo.',
            'same-as-current'   => 'La nueva contraseña debe ser diferente de la contraseña actual.',
        ],

        'current-helper' => 'Introduzca la contraseña actual para verificar su identidad.',

        'notification' => [
            'success' => [
                'title' => 'Contraseña actualizada',
                'body'  => 'La contraseña se ha actualizado correctamente.',
            ],

            'error' => [
                'title' => 'Error al actualizar la contraseña',
                'body'  => 'Se produjo un error al actualizar la contraseña.',
            ],
        ],
    ],
];
