<?php

return [
    'group' => [
        'label' => 'Acceso al Portal',
    ],

    'grant' => [
        'label' => 'Conceder Acceso al Portal',

        'modal' => [
            'heading'     => 'Conceder Acceso al Portal',
            'description' => 'Se enviará un correo de invitación a :email con un enlace para establecer una contraseña para el portal de clientes.',
        ],

        'notification' => [
            'email-missing' => [
                'title' => 'Se requiere una dirección de correo electrónico',
                'body'  => 'Agregue una dirección de correo electrónico a este contacto antes de conceder acceso al portal.',
            ],

            'email-taken' => [
                'title' => 'La dirección de correo electrónico ya está en uso',
                'body'  => 'Otro contacto con acceso al portal ya está usando esta dirección de correo electrónico.',
            ],

            'granted' => [
                'title' => 'Acceso al portal concedido',
                'body'  => 'Se ha enviado una invitación para establecer una contraseña a :email.',
            ],

            'email-failed' => [
                'title' => 'Acceso al portal concedido, pero no se pudo enviar el correo de invitación',
            ],
        ],
    ],

    'change-password' => [
        'label' => 'Cambiar Contraseña',

        'modal' => [
            'heading'     => 'Cambiar Contraseña del Portal',
            'description' => 'Establezca una nueva contraseña del portal de clientes para :email.',
        ],

        'form' => [
            'password' => [
                'label' => 'Nueva Contraseña',
            ],

            'password-confirmation' => [
                'label' => 'Confirmar Nueva Contraseña',
            ],
        ],

        'notification' => [
            'changed' => [
                'title' => 'Contraseña del portal cambiada',
                'body'  => 'El contacto ahora puede iniciar sesión en el portal de clientes con la nueva contraseña.',
            ],
        ],
    ],

    'password-reset' => [
        'label' => 'Enviar Restablecimiento de Contraseña',

        'modal' => [
            'heading'     => 'Enviar Restablecimiento de Contraseña',
            'description' => 'Se enviará un enlace de restablecimiento de contraseña a :email.',
        ],

        'notification' => [
            'sent' => [
                'title' => 'Enlace de restablecimiento de contraseña enviado',
                'body'  => 'Se ha enviado un enlace de restablecimiento de contraseña a :email.',
            ],

            'failed' => [
                'title' => 'No se pudo enviar el enlace de restablecimiento de contraseña',
            ],
        ],
    ],

    'revoke' => [
        'label' => 'Revocar Acceso al Portal',

        'modal' => [
            'heading'     => 'Revocar Acceso al Portal',
            'description' => 'El contacto ya no podrá iniciar sesión en el portal de clientes. Cualquier enlace pendiente de restablecimiento de contraseña será invalidado.',
        ],

        'notification' => [
            'revoked' => [
                'title' => 'Acceso al portal revocado',
                'body'  => 'El contacto ya no puede iniciar sesión en el portal de clientes.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'portal-access' => 'Acceso al Portal',
        ],

        'filters' => [
            'portal-access' => 'Acceso al Portal',
        ],
    ],

    'infolist' => [
        'section' => [
            'title' => 'Portal de Clientes',
        ],

        'entries' => [
            'status' => [
                'label'   => 'Acceso al Portal',
                'granted' => 'Concedido',
                'none'    => 'No Concedido',
            ],

            'email-verified-at' => [
                'label'       => 'Correo Verificado El',
                'placeholder' => 'Nunca',
            ],

            'last-login-at' => [
                'label'       => 'Último Inicio de Sesión en el Portal',
                'placeholder' => 'Nunca',
            ],
        ],
    ],
];
