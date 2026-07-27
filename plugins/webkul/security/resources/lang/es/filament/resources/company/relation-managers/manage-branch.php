<?php

return [
    'form' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Información general',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Información de la sucursal',

                        'fields' => [
                            'company-name'                => 'Nombre de la empresa',
                            'registration-number'         => 'Número de registro',
                            'tax-id'                      => 'Tax ID',
                            'tax-id-tooltip'              => 'El Tax ID es un identificador único de la empresa.',
                            'color'                       => 'Color',
                            'company-id'                  => 'ID de empresa',
                            'company-id-tooltip'          => 'El ID de empresa es un identificador único de la empresa.',
                        ],
                    ],

                    'branding' => [
                        'title'  => 'Personalización de marca',
                        'fields' => [
                            'branch-logo' => 'Logotipo de la sucursal',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Información de dirección',

                'sections' => [
                    'address-information' => [
                        'title' => 'Información de dirección',

                        'fields' => [
                            'street1'                => 'Calle 1',
                            'street2'                => 'Calle 2',
                            'city'                   => 'Ciudad',
                            'zip'                    => 'Código postal',
                            'country'                => 'País',
                            'country-currency-name'  => 'Nombre de la moneda',
                            'country-phone-code'     => 'Código telefónico',
                            'country-code'           => 'Código',
                            'country-name'           => 'Nombre del país',
                            'country-state-required' => 'Provincia obligatoria',
                            'country-zip-required'   => 'Código postal obligatorio',
                            'country-create'         => 'Crear país',
                            'state'                  => 'Provincia',
                            'state-name'             => 'Nombre de la provincia',
                            'state-code'             => 'Código de la provincia',
                            'zip-code'               => 'Código postal',
                            'state-create'           => 'Crear provincia',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Información adicional',

                        'fields' => [
                            'default-currency'        => 'Moneda predeterminada',
                            'currency-name'           => 'Nombre de la moneda',
                            'currency-full-name'      => 'Nombre completo de la moneda',
                            'currency-symbol'         => 'Símbolo de la moneda',
                            'currency-iso-numeric'    => 'ISO numérico de la moneda',
                            'currency-decimal-places' => 'Decimales de la moneda',
                            'currency-rounding'       => 'Redondeo de la moneda',
                            'currency-status'         => 'Estado de la moneda',
                            'currency-create'         => 'Crear moneda',
                            'company-foundation-date' => 'Fecha de fundación de la empresa',
                            'status'                  => 'Estado',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Información de contacto',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Información de contacto',

                        'fields' => [
                            'email-address' => 'Dirección de correo electrónico',
                            'phone-number'  => 'Número de teléfono',
                            'mobile-number' => 'Número de teléfono',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Logotipo',
            'company-name'         => 'Nombre de la sucursal',
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
            'company-name' => 'Nombre de la sucursal',
            'city'         => 'Ciudad',
            'country'      => 'País',
            'state'        => 'Provincia',
            'email'        => 'Correo electrónico',
            'phone'        => 'Teléfono',
            'currency'     => 'Moneda',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'filters' => [
            'trashed' => 'Eliminados',
            'status'  => 'Estado',
            'country' => 'País',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Sucursal creada',
                    'body'  => 'La sucursal se ha creado correctamente.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Sucursal actualizada',
                    'body'  => 'La sucursal se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sucursal eliminada',
                    'body'  => 'La sucursal se ha eliminado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Sucursal restaurada',
                    'body'  => 'La sucursal se ha restaurado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Sucursales restauradas',
                    'body'  => 'Las sucursales se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sucursales eliminadas',
                    'body'  => 'Las sucursales se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Sucursales eliminadas permanentemente',
                    'body'  => 'Las sucursales se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Información general',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Información de la sucursal',

                        'entries' => [
                            'company-name'                => 'Nombre de la empresa',
                            'registration-number'         => 'Número de registro',
                            'tax-id'                      => 'Tax ID',
                            'registration-number-tooltip' => 'El Tax ID es un identificador único de la empresa.',
                            'color'                       => 'Color',
                        ],
                    ],

                    'branding' => [
                        'title'   => 'Personalización de marca',
                        'entries' => [
                            'branch-logo' => 'Logotipo de la sucursal',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Información de dirección',

                'sections' => [
                    'address-information' => [
                        'title' => 'Información de dirección',

                        'entries' => [
                            'street1'                => 'Calle 1',
                            'street2'                => 'Calle 2',
                            'city'                   => 'Ciudad',
                            'zip'                    => 'Código postal',
                            'country'                => 'País',
                            'country-currency-name'  => 'Nombre de la moneda',
                            'country-phone-code'     => 'Código telefónico',
                            'country-code'           => 'Código',
                            'country-name'           => 'Nombre del país',
                            'country-state-required' => 'Provincia obligatoria',
                            'country-zip-required'   => 'Código postal obligatorio',
                            'country-create'         => 'Crear país',
                            'state'                  => 'Provincia',
                            'state-name'             => 'Nombre de la provincia',
                            'state-code'             => 'Código de la provincia',
                            'zip-code'               => 'Código postal',
                            'state-create'           => 'Crear provincia',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Información adicional',

                        'entries' => [
                            'default-currency'        => 'Moneda predeterminada',
                            'currency-name'           => 'Nombre de la moneda',
                            'currency-full-name'      => 'Nombre completo de la moneda',
                            'currency-symbol'         => 'Símbolo de la moneda',
                            'currency-iso-numeric'    => 'ISO numérico de la moneda',
                            'currency-decimal-places' => 'Decimales de la moneda',
                            'currency-rounding'       => 'Redondeo de la moneda',
                            'currency-status'         => 'Estado de la moneda',
                            'currency-create'         => 'Crear moneda',
                            'company-foundation-date' => 'Fecha de fundación de la empresa',
                            'status'                  => 'Estado',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Información de contacto',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Información de contacto',

                        'entries' => [
                            'email-address' => 'Dirección de correo electrónico',
                            'phone-number'  => 'Número de teléfono',
                            'mobile-number' => 'Número de teléfono',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
