<?php

return [
    'title' => 'Empresas',

    'navigation' => [
        'title' => 'Empresas',
        'group' => 'Configuración',
    ],

    'global-search' => [
        'email' => 'Correo electrónico',
    ],

    'form' => [
        'sections' => [
            'company-information' => [
                'title'  => 'Información de la empresa',
                'fields' => [
                    'name'                  => 'Nombre de la empresa',
                    'registration-number'   => 'Número de registro',
                    'company-id'            => 'ID de la empresa',
                    'tax-id'                => 'Identificación fiscal',
                    'tax-id-tooltip'        => 'La identificación fiscal es un identificador único de su empresa.',
                    'website'               => 'Sitio web',
                ],
            ],

            'address-information' => [
                'title'  => 'Información de dirección',

                'fields' => [
                    'street1'        => 'Calle 1',
                    'street2'        => 'Calle 2',
                    'city'           => 'Ciudad',
                    'zipcode'        => 'Código postal',
                    'country'        => 'País',
                    'currency-name'  => 'Nombre de la moneda',
                    'phone-code'     => 'Código telefónico',
                    'code'           => 'Código',
                    'country-name'   => 'Nombre del país',
                    'state-required' => 'Estado obligatorio',
                    'zip-required'   => 'Código postal obligatorio',
                    'create-country' => 'Crear país',
                    'state'          => 'Estado',
                    'state-name'     => 'Nombre del estado',
                    'state-code'     => 'Código del estado',
                    'create-state'   => 'Crear estado',
                ],
            ],

            'additional-information' => [
                'title' => 'Información adicional',

                'fields' => [
                    'default-currency'        => 'Moneda predeterminada',
                    'currency-name'           => 'Nombre de la moneda',
                    'currency-full-name'      => 'Nombre completo de la moneda',
                    'currency-symbol'         => 'Símbolo de la moneda',
                    'currency-iso-numeric'    => 'Código ISO numérico de la moneda',
                    'currency-decimal-places' => 'Decimales de la moneda',
                    'currency-rounding'       => 'Redondeo de la moneda',
                    'currency-status'         => 'Estado de la moneda',
                    'company-foundation-date' => 'Fecha de fundación de la empresa',
                    'currency-create'         => 'Crear moneda',
                    'status'                  => 'Estado',
                ],
            ],

            'branding' => [
                'title'  => 'Personalización de marca',
                'fields' => [
                    'company-logo' => 'Logotipo de la empresa',
                    'color'        => 'Color',
                ],
            ],

            'contact-information' => [
                'title'  => 'Información de contacto',
                'fields' => [
                    'email'  => 'Dirección de correo electrónico',
                    'phone'  => 'Número de teléfono',
                    'mobile' => 'Número de teléfono',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Logotipo',
            'company-name'         => 'Nombre de la empresa',
            'branches'             => 'Sucursales',
            'email'                => 'Correo electrónico',
            'city'                 => 'Ciudad',
            'country'              => 'País',
            'currency'             => 'Moneda',
            'status'               => 'Estado',
            'created-at'           => 'Creado el',
            'updated-at'           => 'Actualizado el',
        ],

        'groups' => [
            'company-name' => 'Nombre de la empresa',
            'city'         => 'Ciudad',
            'country'      => 'País',
            'state'        => 'Estado',
            'email'        => 'Correo electrónico',
            'phone'        => 'Teléfono',
            'currency'     => 'Moneda',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'filters' => [
            'status'  => 'Estado',
            'country' => 'País',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Empresa editada',
                    'body'  => 'La empresa se ha editado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Empresa eliminada',
                    'body'  => 'La empresa se ha eliminado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Empresa restaurada',
                    'body'  => 'La empresa se ha restaurado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Empresas restauradas',
                    'body'  => 'Las empresas se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Empresas eliminadas',
                    'body'  => 'Las empresas se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Empresas eliminadas permanentemente',
                    'body'  => 'Las empresas se han eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Empresas creadas',
                    'body'  => 'Las empresas se han creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'company-information' => [
                'title'   => 'Información de la empresa',
                'entries' => [
                    'name'                  => 'Nombre de la empresa',
                    'registration-number'   => 'Número de registro',
                    'company-id'            => 'ID de la empresa',
                    'tax-id'                => 'Identificación fiscal',
                    'tax-id-tooltip'        => 'La identificación fiscal es un identificador único de su empresa.',
                    'website'               => 'Sitio web',
                ],
            ],

            'address-information' => [
                'title'  => 'Información de dirección',

                'entries' => [
                    'street1'        => 'Calle 1',
                    'street2'        => 'Calle 2',
                    'city'           => 'Ciudad',
                    'zipcode'        => 'Código postal',
                    'country'        => 'País',
                    'currency-name'  => 'Nombre de la moneda',
                    'phone-code'     => 'Código telefónico',
                    'code'           => 'Código',
                    'country-name'   => 'Nombre del país',
                    'state-required' => 'Estado obligatorio',
                    'zip-required'   => 'Código postal obligatorio',
                    'create-country' => 'Crear país',
                    'state'          => 'Estado',
                    'state-name'     => 'Nombre del estado',
                    'state-code'     => 'Código del estado',
                    'create-state'   => 'Crear estado',
                ],
            ],

            'additional-information' => [
                'title' => 'Información adicional',

                'entries' => [
                    'default-currency'        => 'Moneda predeterminada',
                    'currency-name'           => 'Nombre de la moneda',
                    'currency-full-name'      => 'Nombre completo de la moneda',
                    'currency-symbol'         => 'Símbolo de la moneda',
                    'currency-iso-numeric'    => 'Código ISO numérico de la moneda',
                    'currency-decimal-places' => 'Decimales de la moneda',
                    'currency-rounding'       => 'Redondeo de la moneda',
                    'currency-status'         => 'Estado de la moneda',
                    'company-foundation-date' => 'Fecha de fundación de la empresa',
                    'currency-create'         => 'Crear moneda',
                    'status'                  => 'Estado',
                ],
            ],

            'branding' => [
                'title'   => 'Personalización de marca',
                'entries' => [
                    'company-logo' => 'Logotipo de la empresa',
                    'color'        => 'Color',
                ],
            ],

            'contact-information' => [
                'title'   => 'Información de contacto',
                'entries' => [
                    'email'  => 'Dirección de correo electrónico',
                    'phone'  => 'Número de teléfono',
                    'mobile' => 'Número de teléfono',
                ],
            ],
        ],
    ],
];
