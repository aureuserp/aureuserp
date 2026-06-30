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
                    'company-id'            => 'ID de empresa',
                    'tax-id'                => 'NIF',
                    'tax-id-tooltip'        => 'El NIF es un identificador único para su empresa.',
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
                    'state-required' => 'Estado/Provincia obligatorio',
                    'zip-required'   => 'Código postal obligatorio',
                    'create-country' => 'Crear país',
                    'state'          => 'Estado/Provincia',
                    'state-name'     => 'Nombre del estado/provincia',
                    'state-code'     => 'Código del estado/provincia',
                    'create-state'   => 'Crear estado/provincia',
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
            'logo'         => 'Logotipo',
            'company-name' => 'Nombre de la empresa',
            'branches'     => 'Sucursales',
            'email'        => 'Correo electrónico',
            'city'         => 'Ciudad',
            'country'      => 'País',
            'currency'     => 'Moneda',
            'created-by'   => 'Creado por',
            'status'       => 'Estado',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'groups' => [
            'company-name' => 'Nombre de la empresa',
            'city'         => 'Ciudad',
            'country'      => 'País',
            'state'        => 'Estado/Provincia',
            'email'        => 'Correo electrónico',
            'phone'        => 'Teléfono',
            'currency'     => 'Moneda',
            'created-by'   => 'Creado por',
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

                    'default-company' => [
                        'title' => 'No se puede eliminar la empresa',
                        'body'  => 'Esta empresa está configurada como la empresa predeterminada en la configuración de Gestionar usuarios. Cambie la empresa predeterminada antes de eliminarla.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Empresa restaurada',
                    'body'  => 'La empresa se ha restaurado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Empresa eliminada permanentemente',
                        'body'  => 'La empresa se ha eliminado permanentemente correctamente.',
                    ],
                    'error' => [
                        'title' => 'No se puede eliminar permanentemente la empresa',
                        'body'  => 'Esta empresa está asociada a registros existentes y no se puede eliminar.',
                    ],
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
                    'error' => [
                        'title' => 'No se pueden eliminar permanentemente las empresas',
                        'body'  => 'Una o más empresas están asociadas a registros existentes y no se pueden eliminar.',
                    ],
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
                    'company-id'            => 'ID de empresa',
                    'tax-id'                => 'NIF',
                    'tax-id-tooltip'        => 'El NIF es un identificador único para su empresa.',
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
                    'state-required' => 'Estado/Provincia obligatorio',
                    'zip-required'   => 'Código postal obligatorio',
                    'create-country' => 'Crear país',
                    'state'          => 'Estado/Provincia',
                    'state-name'     => 'Nombre del estado/provincia',
                    'state-code'     => 'Código del estado/provincia',
                    'create-state'   => 'Crear estado/provincia',
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
