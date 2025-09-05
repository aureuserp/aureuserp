<?php

return [
    'form' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Información General',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Información de la Sucursal',

                        'fields' => [
                            'company-name'                => 'Nombre de la Empresa',
                            'registration-number'         => 'Número de Registro',
                            'tax-id'                      => 'ID Fiscal',
                            'tax-id-tooltip'              => 'El ID Fiscal es un identificador único para su empresa.',
                            'color'                       => 'Color',
                            'company-id'                  => 'ID de la Empresa',
                            'company-id-tooltip'          => 'El ID de la Empresa es un identificador único para su empresa.',
                        ],
                    ],

                    'branding' => [
                        'title'  => 'Marca',
                        'fields' => [
                            'branch-logo' => 'Logotipo de la Sucursal',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Información de la Dirección',

                'sections' => [
                    'address-information' => [
                        'title' => 'Información de la Dirección',

                        'fields' => [
                            'street1'                => 'Calle 1',
                            'street2'                => 'Calle 2',
                            'city'                   => 'Ciudad',
                            'zip'                    => 'Código Postal',
                            'country'                => 'País',
                            'country-currency-name'  => 'Nombre de la Moneda del País',
                            'country-phone-code'     => 'Código Telefónico del País',
                            'country-code'           => 'Código del País',
                            'country-name'           => 'Nombre del País',
                            'country-state-required' => 'Estado Requerido',
                            'country-zip-required'   => 'Código Postal Requerido',
                            'country-create'         => 'Crear País',
                            'state'                  => 'Estado',
                            'state-name'             => 'Nombre del Estado',
                            'state-code'             => 'Código del Estado',
                            'zip-code'               => 'Código Postal',
                            'state-create'           => 'Crear Estado',
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
                            'currency-create'         => 'Crear Moneda',
                            'company-foundation-date' => 'Fecha de Fundación de la Empresa',
                            'status'                  => 'Estado',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Información de Contacto',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Información de Contacto',

                        'fields' => [
                            'email-address' => 'Correo Electrónico',
                            'phone-number'  => 'Número de Teléfono',
                            'mobile-number' => 'Número de Móvil',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Logotipo',
            'company-name'         => 'Nombre de la Sucursal',
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
            'company-name' => 'Nombre de la Sucursal',
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
            'trashed' => 'Eliminados',
            'status'  => 'Estado',
            'country' => 'País',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Sucursal creada',
                    'body'  => 'La sucursal ha sido creada exitosamente.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Sucursal actualizada',
                    'body'  => 'La sucursal ha sido actualizada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sucursal eliminada',
                    'body'  => 'La sucursal ha sido eliminada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Sucursal restaurada',
                    'body'  => 'La sucursal ha sido restaurada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Sucursales restauradas',
                    'body'  => 'Las sucursales han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sucursales eliminadas',
                    'body'  => 'Las sucursales han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Sucursales eliminadas permanentemente',
                    'body'  => 'Las sucursales han sido eliminadas permanentemente exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Información General',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Información de la Sucursal',

                        'entries' => [
                            'company-name'                => 'Nombre de la Empresa',
                            'registration-number'         => 'Número de Registro',
                            'registration-number-tooltip' => 'El ID Fiscal es un identificador único para su empresa.',
                            'color'                       => 'Color',
                        ],
                    ],

                    'branding' => [
                        'title'   => 'Marca',
                        'entries' => [
                            'branch-logo' => 'Logotipo de la Sucursal',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Información de la Dirección',

                'sections' => [
                    'address-information' => [
                        'title' => 'Información de la Dirección',

                        'entries' => [
                            'street1'                => 'Calle 1',
                            'street2'                => 'Calle 2',
                            'city'                   => 'Ciudad',
                            'zip'                    => 'Código Postal',
                            'country'                => 'País',
                            'country-currency-name'  => 'Nombre de la Moneda del País',
                            'country-phone-code'     => 'Código Telefónico del País',
                            'country-code'           => 'Código del País',
                            'country-name'           => 'Nombre del País',
                            'country-state-required' => 'Estado Requerido',
                            'country-zip-required'   => 'Código Postal Requerido',
                            'country-create'         => 'Crear País',
                            'state'                  => 'Estado',
                            'state-name'             => 'Nombre del Estado',
                            'state-code'             => 'Código del Estado',
                            'zip-code'               => 'Código Postal',
                            'state-create'           => 'Crear Estado',
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
                            'currency-create'         => 'Crear Moneda',
                            'company-foundation-date' => 'Fecha de Fundación de la Empresa',
                            'status'                  => 'Estado',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Información de Contacto',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Información de Contacto',

                        'entries' => [
                            'email-address' => 'Correo Electrónico',
                            'phone-number'  => 'Número de Teléfono',
                            'mobile-number' => 'Número de Móvil',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
