<?php

return [
    'global-search' => [
        'email' => 'Correo electrónico',
        'phone' => 'Teléfono',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'company'    => 'Empresa',
                    'avatar'     => 'Avatar',
                    'tax-id'     => 'Tax ID',
                    'job-title'  => 'Puesto de trabajo',
                    'phone'      => 'Teléfono',
                    'mobile'     => 'Móvil',
                    'email'      => 'Correo electrónico',
                    'website'    => 'Sitio web',
                    'title'      => 'Tratamiento',
                    'name'       => 'Nombre',
                    'short-name' => 'Nombre corto',
                    'tags'       => 'Etiquetas',
                    'color'      => 'Color',
                ],

                'address' => [
                    'title' => 'Dirección',

                    'fields' => [
                        'street1'  => 'Calle 1',
                        'street2'  => 'Calle 2',
                        'city'     => 'Ciudad',
                        'zip'      => 'Código postal',
                        'state'    => 'Provincia',
                        'country'  => 'País',
                        'name'     => 'Nombre',
                        'code'     => 'Código',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Ventas y compras',

                'fields' => [
                    'responsible'           => 'Responsable',
                    'responsible-hint-text' => 'Este es el comercial interno responsable de este cliente',
                    'company-id'            => 'ID de empresa',
                    'company-id-hint-text'  => 'El número de registro de la empresa, usado si es diferente del Tax ID. Debe ser único entre todos los contactos dentro del mismo país.',
                    'reference'             => 'Referencia',
                    'industry'              => 'Sector',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'parent'     => 'Contacto principal',
            'work-email' => 'Correo electrónico de trabajo',
            'work-phone' => 'Teléfono de trabajo',
        ],

        'groups' => [
            'account-type' => 'Tipo de cuenta',
            'parent'       => 'Contacto principal',
            'title'        => 'Tratamiento',
            'job-title'    => 'Puesto de trabajo',
            'industry'     => 'Sector',
        ],

        'filters' => [
            'account-type'     => 'Tipo de cuenta',
            'name'             => 'Nombre',
            'email'            => 'Correo electrónico',
            'parent'           => 'Contacto principal',
            'title'            => 'Tratamiento',
            'tax-id'           => 'Tax ID',
            'phone'            => 'Teléfono',
            'mobile'           => 'Móvil',
            'job-title'        => 'Puesto de trabajo',
            'website'          => 'Sitio web',
            'company-registry' => 'Registro mercantil',
            'responsible'      => 'Responsable',
            'reference'        => 'Referencia',
            'parent'           => 'Contacto principal',
            'creator'          => 'Creador',
            'company'          => 'Empresa',
            'industry'         => 'Sector',
            'industry'         => 'Sector',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Contacto actualizado',
                    'body'  => 'El contacto se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Contacto restaurado',
                    'body'  => 'El contacto se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Contacto eliminado',
                    'body'  => 'El contacto se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contacto eliminado de forma permanente',
                        'body'  => 'El contacto se ha eliminado de forma permanente correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el contacto',
                        'body'  => 'El contacto no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Contactos restaurados',
                    'body'  => 'Los contactos se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Contactos eliminados',
                    'body'  => 'Los contactos se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contactos eliminados de forma permanente',
                        'body'  => 'Los contactos se han eliminado de forma permanente correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los contactos',
                        'body'  => 'Los contactos no se pueden eliminar porque están actualmente en uso.',
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
                    'company'    => 'Empresa',
                    'avatar'     => 'Avatar',
                    'tax-id'     => 'Tax ID',
                    'job-title'  => 'Puesto de trabajo',
                    'phone'      => 'Teléfono',
                    'mobile'     => 'Móvil',
                    'email'      => 'Correo electrónico',
                    'website'    => 'Sitio web',
                    'title'      => 'Tratamiento',
                    'name'       => 'Nombre',
                    'short-name' => 'Nombre corto',
                    'tags'       => 'Etiquetas',
                ],

                'address' => [
                    'title' => 'Dirección',

                    'fields' => [
                        'street1'  => 'Calle 1',
                        'street2'  => 'Calle 2',
                        'city'     => 'Ciudad',
                        'zip'      => 'Código postal',
                        'state'    => 'Provincia',
                        'country'  => 'País',
                        'name'     => 'Nombre',
                        'code'     => 'Código',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Ventas y compras',

                'fields' => [
                    'responsible'           => 'Responsable',
                    'responsible-hint-text' => 'Este es el comercial interno responsable de este cliente',
                    'company-id'            => 'ID de empresa',
                    'company-id-hint-text'  => 'El número de registro de la empresa. Úselo si es diferente del Tax ID. Debe ser único entre todos los contactos de un mismo país',
                    'reference'             => 'Referencia',
                    'industry'              => 'Sector',
                ],
            ],
        ],
    ],
];
