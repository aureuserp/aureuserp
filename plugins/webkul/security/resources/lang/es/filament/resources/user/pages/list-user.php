<?php

return [
    'tabs' => [
        'all'      => 'Todos los Usuarios',
        'archived' => 'Usuarios Archivados',
    ],

    'header-actions' => [
        'invite' => [
            'title' => 'Invitar Usuario',
            'modal' => [
                'submit-action-label' => 'Invitar Usuario',
            ],
            'form' => [
                'email' => 'Correo Electrónico',
            ],
            'notification' => [
                'success' => [
                    'title' => 'Usuario invitado',
                    'body'  => 'El usuario ha sido invitado exitosamente.',
                ],
                'error' => [
                    'title' => 'Fallo al Invitar Usuario',
                    'body'  => 'El sistema encontró un error inesperado al intentar enviar la invitación al usuario.',
                ],

                'default-company-error' => [
                    'title' => 'Empresa por Defecto No Establecida',
                    'body'  => 'Por favor, configura la empresa por defecto desde los ajustes antes de invitar a un usuario.',
                ],
            ],
        ],

        'create' => [
            'label' => 'Nuevo Usuario',
        ],
    ],
];
