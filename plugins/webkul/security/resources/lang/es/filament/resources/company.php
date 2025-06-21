<?php

return [
    'title' => 'Empresas',

    'navigation' => [
        'title' => 'Empresas',
        'group' => 'Ajustes',
    ],

    'global-search' => [
        'name'  => 'Nombre',
        'email' => 'Correo Electrónico',
    ],

    'form' => [
        'sections' => [
            'company-information' => [
                'title'  => 'Información de la Empresa',
                'fields' => [
                    'name'                  => 'Nombre de la Empresa',
                    'registration-number'   => 'Número de Registro',
                    'company-id'            => 'ID de la Empresa',
                    'tax-id'                => 'ID Fiscal',
                    'tax-id-tooltip'        => 'El ID Fiscal es un identificador único para su empresa.',
                    'website'               => 'Sitio Web',
                ],
            ],

            'address-information' => [
                'title'  => 'Información de la Dirección',

                'fields' => [
                    'street1'        => 'Calle 1',
                    'street2'        => 'Calle 2',
                    'city'           => 'Ciudad',
                    'zipcode'        => 'Código Postal',
                    'country'        => 'País',
                    'currency-name'  => 'Nombre de la Moneda',
                    'phone-code'     => 'Código Telefónico',
                    'code'           => 'Código',
                    'country-name'   => 'Nombre del País',
                    'state-required' => 'Estado Requerido',
                    'zip-required'   => 'Código Postal Requerido',
                    'create-country' => 'Crear País',
                    'state'          => 'Estado',
                    'state-name'     => 'Nombre del Estado',
                    'state-code'     => 'Código del Estado',
                    'create-state'   => 'Crear Estado',
                ],
            ],

            'additional-information' => [
                'title' => 'Información Adicional',

                'fields' => [
                    'default-currency'        => 'Moneda por Defecto',
                    'currency-name'           => 'Nombre de la Moneda',
                    'currency-full-name'      => 'Nombre Completo de la Moneda',
                    'currency-symbol'         => 'Símbolo de la Moneda',
                    'currency-iso-numeric'    => 'ISO Numérico de la Moneda',
                    'currency-decimal-places' => 'Posiciones Decimales de la Moneda',
                    'currency-rounding'       => 'Redondeo de la Moneda',
                    'currency-status'         => 'Estado de la Moneda',
                    'company-foundation-date' => 'Fecha de Fundación de la Empresa',
                    'currency-create'         => 'Crear Moneda',
                    'status'                  => 'Estado',
                ],
            ],

            'branding' => [
                'title'  => 'Marca',
                'fields' => [
                    'company-logo' => 'Logotipo de la Empresa',
                    'color'        => 'Color',
                ],
            ],

            'contact-information' => [
                'title'  => 'Información de Contacto',
                'fields' => [
                    'email'  => 'Correo Electrónico',
                    'phone'  => 'Número de Teléfono',
                    'mobile' => 'Número de Móvil',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Logotipo',
            'company-name'         => 'Nombre de la Empresa',
            'branches'             => 'Sucursales',
            'email'                => 'Correo Electrónico',
            'city'                 => 'Ciudad',
            'country'              => 'País',
            'currency'             => 'Moneda',
            'status'               => 'Estado',
            'created-at'           => 'Creado El',
            'updated-at'           => 'Actualizado El',
        ],

        'groups' => [
            'company-name' => 'Nombre de la Empresa',
            'city'         => 'Ciudad',
            'country'      => 'País',
            'state'        => 'Estado',
            'email'        => 'Correo Electrónico',
            'phone'        => 'Teléfono',
            'currency'     => 'Moneda',
            'created-at'   => 'Creado El',
            'updated-at'   => 'Actualizado El',
        ],

        'filters' => [
            'status'  => 'Estado',
            'country' => 'País',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Empresa editada',
                    'body'  => 'La empresa ha sido editada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Empresa eliminada',
                    'body'  => 'La empresa ha sido eliminada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Empresa restaurada',
                    'body'  => 'La empresa ha sido restaurada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Empresas restauradas',
                    'body'  => 'Las empresas han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Empresas eliminadas',
                    'body'  => 'Las empresas han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Empresas eliminadas permanentemente',
                    'body'  => 'Las empresas han sido eliminadas permanentemente exitosamente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Empresas creadas',
                    'body'  => 'Las empresas han sido creadas exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'company-information' => [
                'title'   => 'Información de la Empresa',
                'entries' => [
                    'name'                  => 'Nombre de la Empresa',
                    'registration-number'   => 'Número de Registro',
                    'company-id'            => 'ID de la Empresa',
                    'tax-id'                => 'ID Fiscal',
                    'tax-id-tooltip'        => 'El ID Fiscal es un identificador único para su empresa.',
                    'website'               => 'Sitio Web',
                ],
            ],

            'address-information' => [
                'title'  => 'Información de la Dirección',

                'entries' => [
                    'street1'        => 'Calle 1',
                    'street2'        => 'Calle 2',
                    'city'           => 'Ciudad',
                    'zipcode'        => 'Código Postal',
                    'country'        => 'País',
                    'currency-name'  => 'Nombre de la Moneda',
                    'phone-code'     => 'Código Telefónico',
                    'code'           => 'Código',
                    'country-name'   => 'Nombre del País',
                    'state-required' => 'Estado Requerido',
                    'zip-required'   => 'Código Postal Requerido',
                    'create-country' => 'Crear País',
                    'state'          => 'Estado',
                    'state-name'     => 'Nombre del Estado',
                    'state-code'     => 'Código del Estado',
                    'create-state'   => 'Crear Estado',
                ],
            ],

            'additional-information' => [
                'title' => 'Información Adicional',

                'entries' => [
                    'default-currency'        => 'Moneda por Defecto',
                    'currency-name'           => 'Nombre de la Moneda',
                    'currency-full-name'      => 'Nombre Completo de la Moneda',
                    'currency-symbol'         => 'Símbolo de la Moneda',
                    'currency-iso-numeric'    => 'ISO Numérico de la Moneda',
                    'currency-decimal-places' => 'Posiciones Decimales de la Moneda',
                    'currency-rounding'       => 'Redondeo de la Moneda',
                    'currency-status'         => 'Estado de la Moneda',
                    'company-foundation-date' => 'Fecha de Fundación de la Empresa',
                    'currency-create'         => 'Crear Moneda',
                    'status'                  => 'Estado',
                ],
            ],

            'branding' => [
                'title'   => 'Marca',
                'entries' => [
                    'company-logo' => 'Logotipo de la Empresa',
                    'color'        => 'Color',
                ],
            ],

            'contact-information' => [
                'title'   => 'Información de Contacto',
                'entries' => [
                    'email'  => 'Correo Electrónico',
                    'phone'  => 'Número de Teléfono',
                    'mobile' => 'Número de Móvil',
                ],
            ],
        ],
    ],
];
