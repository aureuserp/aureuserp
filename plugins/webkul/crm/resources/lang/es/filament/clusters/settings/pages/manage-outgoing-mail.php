<?php

return [
    'title' => 'Servidor de correo saliente',

    'actions' => [
        'test' => [
            'label'   => 'Enviar correo de prueba',
            'subject' => 'Correo de prueba',
            'body'    => '<p>Su servidor de correo saliente funciona.</p>',
            'sent'    => 'Correo de prueba enviado a :email.',
            'failed'  => 'No se pudo enviar el correo de prueba.',
        ],
    ],

    'form' => [
        'active'              => 'Usar un servidor de correo saliente propio',
        'active-helper'       => 'Los correos enviados desde el ERP (por ejemplo "Enviar correo" del CRM) pasan por este servidor. Si está desactivado, se usa el servidor de la configuración de la aplicación.',
        'host'                => 'Servidor SMTP',
        'encryption'          => 'Cifrado',
        'port'                => 'Puerto',
        'username'            => 'Usuario',
        'password'            => 'Contraseña',
        'password-helper'     => 'Déjela vacía para conservar la contraseña actual.',
        'from-address'        => 'Dirección del remitente',
        'from-address-helper' => 'Dirección desde la que se envían los correos. Vacía = la predeterminada de la aplicación.',
        'from-name'           => 'Nombre del remitente',

        'encryptions' => [
            'tls'  => 'STARTTLS (puerto 587)',
            'ssl'  => 'SSL/TLS (puerto 465)',
            'none' => 'Ninguno (puerto 25)',
        ],
    ],
];
