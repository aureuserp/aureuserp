<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'company'           => 'Empresa',
                    'avatar'            => 'Avatar',
                    'tax-id'            => 'CUIT', // "Tax ID" se traduce como "CUIT" en Argentina
                    'job-title'         => 'Cargo',
                    'phone'             => 'Teléfono',
                    'mobile'            => 'Móvil',
                    'email'             => 'Email',
                    'website'           => 'Sitio Web',
                    'title'             => 'Título',
                    'name'              => 'Nombre',
                    'short-name'        => 'Nombre Corto',
                    'tags'              => 'Etiquetas',
                    'color'             => 'Color',
                ],
            ],

            'address' => [
                'title' => 'Dirección',

                'fields' => [
                    'street1' => 'Calle 1',
                    'street2' => 'Calle 2',
                    'city'    => 'Ciudad',
                    'zip'     => 'Código Postal',
                    'state'   => 'Estado', // Corregido: de 'Provincia' a 'Estado'
                    'country' => 'País',
                    'name'    => 'Nombre',
                    'code'    => 'Código',
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Ventas y Compras',

                'fields' => [
                    'responsible'           => 'Responsable',
                    'responsible-hint-text' => 'Este es el vendedor interno responsable de este cliente',
                    'company-id'            => 'CUIT de la Empresa', // O "Número de Registro de la Empresa", dependiendo del contexto
                    'company-id-hint-text'  => 'El número de registro de la empresa, utilizado si es diferente del CUIT. Debe ser único entre todos los partners dentro del mismo país.',
                    'reference'             => 'Referencia',
                    'industry'              => 'Industria',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'parent' => 'Padre',
        ],

        'groups' => [
            'account-type' => 'Tipo de Cuenta',
            'parent'       => 'Padre',
            'title'        => 'Título',
            'job-title'    => 'Cargo',
            'industry'     => 'Industria',
        ],

        'filters' => [
            'account-type'       => 'Tipo de Cuenta',
            'name'               => 'Nombre',
            'email'              => 'Email',
            'parent'             => 'Padre',
            'title'              => 'Título',
            'tax-id'             => 'CUIT',
            'phone'              => 'Teléfono',
            'mobile'             => 'Móvil',
            'job-title'          => 'Cargo',
            'website'            => 'Sitio Web',
            'company-registry'   => 'Registro de la Empresa',
            'responsible'        => 'Responsable',
            'reference'          => 'Referencia',
            'creator'            => 'Creador',
            'company'            => 'Empresa',
            'industry'           => 'Industria',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Contacto actualizado',
                    'body'  => 'El contacto ha sido actualizado exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Contacto restaurado',
                    'body'  => 'El contacto ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Contacto eliminado',
                    'body'  => 'El contacto ha sido eliminado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contacto eliminado permanentemente',
                        'body'  => 'El contacto ha sido eliminado permanentemente exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el contacto',
                        'body'  => 'El contacto no puede ser eliminado porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Contactos restaurados',
                    'body'  => 'Los contactos han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Contactos eliminados',
                    'body'  => 'Los contactos han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contactos eliminados permanentemente',
                        'body'  => 'Los contactos han sido eliminados permanentemente exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los contactos',
                        'body'  => 'Los contactos no pueden ser eliminados porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'company'           => 'Empresa',
                    'avatar'            => 'Avatar',
                    'tax-id'            => 'CUIT',
                    'job-title'         => 'Cargo',
                    'phone'             => 'Teléfono',
                    'mobile'            => 'Móvil',
                    'email'             => 'Email',
                    'website'           => 'Sitio Web',
                    'title'             => 'Título',
                    'name'              => 'Nombre',
                    'short-name'        => 'Nombre Corto',
                    'tags'              => 'Etiquetas',
                ],
            ],

            'address' => [
                'title' => 'Dirección',

                'fields' => [
                    'street1' => 'Calle 1',
                    'street2' => 'Calle 2',
                    'city'    => 'Ciudad',
                    'zip'     => 'Código Postal',
                    'state'   => 'Estado', // Corregido: de 'Provincia' a 'Estado'
                    'country' => 'País',
                    'name'    => 'Nombre',
                    'code'    => 'Código',
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Ventas y Compras',

                'fields' => [
                    'responsible'           => 'Responsable',
                    'responsible-hint-text' => 'Este es el vendedor interno responsable de este cliente',
                    'company-id'            => 'CUIT de la Empresa',
                    'company-id-hint-text'  => 'El número de registro de la empresa. Utilícelo si es diferente del CUIT. Debe ser único entre todos los partners del mismo país',
                    'reference'             => 'Referencia',
                    'industry'              => 'Industria',
                ],
            ],
        ],
    ],
];
