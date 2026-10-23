<?php

return [
    'model-label'        => 'Servidor de correo entrante',
    'plural-model-label' => 'Servidores de correo entrante',

    'navigation' => [
        'title' => 'Servidores de correo entrante',
        'group' => 'Correo',
    ],

    'header-actions' => [
        'create' => [
            'label'        => 'Nuevo servidor de correo',
            'notification' => 'Servidor de correo creado',
        ],
    ],

    'form' => [
        'name'                        => 'Nombre',
        'name-placeholder'            => 'p. ej. Buzón de ventas',
        'host'                        => 'Servidor (host IMAP)',
        'encryption'                  => 'Cifrado',
        'port'                        => 'Puerto',
        'folder'                      => 'Carpeta',
        'username'                    => 'Usuario',
        'password'                    => 'Contraseña',
        'password-helper'             => 'Déjelo vacío para mantener la contraseña actual.',
        'validate-certificate'        => 'Validar certificado SSL',
        'validate-certificate-helper' => 'Desactívelo solo para servidores con un certificado autofirmado.',
        'active'                      => 'Activo',
        'active-helper'               => 'Se revisa cada minuto en busca de correos nuevos.',

        'encryptions' => [
            'ssl'  => 'SSL/TLS (puerto 993)',
            'tls'  => 'STARTTLS (puerto 143)',
            'none' => 'Ninguno (puerto 143)',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nombre',
            'host'         => 'Servidor',
            'folder'       => 'Carpeta',
            'active'       => 'Activo',
            'last-fetched' => 'Última revisión',
            'last-error'   => 'Último error',
        ],

        'actions' => [
            'test' => [
                'label'   => 'Probar conexión',
                'success' => 'Conexión correcta: :count correo(s) en la carpeta.',
                'failure' => 'Error de conexión',
            ],

            'fetch' => [
                'label'        => 'Obtener ahora',
                'queued'       => 'Obteniendo correos',
                'queued-body'  => 'Los correos nuevos se leen en segundo plano (el worker de la cola debe estar en ejecución). Vea el resultado en Correos recibidos.',
            ],

            'edit' => [
                'notification' => 'Servidor de correo actualizado',
            ],

            'delete' => [
                'notification' => 'Servidor de correo eliminado',
            ],
        ],

        'empty' => [
            'heading'     => 'No hay servidor de correo entrante',
            'description' => 'Añada el buzón que recibe los correos enviados a los alias de sus equipos de ventas.',
        ],
    ],
];
