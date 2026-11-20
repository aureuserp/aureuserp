<?php

return [
    'title' => 'Pasarela SMS',

    'navigation' => [
        'group' => 'SMS',
    ],

    'actions' => [
        'test' => [
            'label'   => 'Enviar SMS de prueba',
            'number'  => 'Número de móvil',
            'message' => 'Tu pasarela SMS funciona.',
            'sent'    => 'SMS de prueba enviado a :number.',
            'failed'  => 'No se pudo enviar el SMS de prueba.',
        ],
    ],

    'form' => [
        'active'           => 'Usar estos datos de pasarela',
        'active-helper'    => 'Los SMS enviados desde el ERP (por ejemplo "Enviar SMS" en CRM) salen por esta cuenta de MSG91. Si está desactivado, se usan los datos de MSG91 de la configuración de la aplicación.',
        'auth-key'         => 'Clave de autenticación',
        'auth-key-helper'  => 'Déjala vacía para conservar la clave actual.',
        'sender-id'        => 'ID de remitente',
        'sender-id-helper' => 'Hasta 11 caracteres, según lo aprobado por tu proveedor.',
        'route'            => 'Ruta',

        'routes' => [
            'transactional' => 'Transaccional',
            'promotional'   => 'Promocional',
        ],
    ],
];
