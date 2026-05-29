<?php

return [
    'navigation' => [
        'title' => 'Tipos de paquete',
        'group' => 'Entrega',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name'       => 'Nombre',
                    'barcode'    => 'Código de barras',
                    'company'    => 'Empresa',
                    'weight'     => 'Peso',
                    'max-weight' => 'Peso máximo',

                    'fieldsets' => [
                        'size' => [
                            'title' => 'Tamaño',

                            'fields' => [
                                'length' => 'Largo',
                                'width'  => 'Ancho',
                                'height' => 'Alto',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'barcode'    => 'Código de barras',
            'weight'     => 'Peso',
            'max-weight' => 'Peso máximo',
            'width'      => 'Ancho',
            'height'     => 'Alto',
            'length'     => 'Largo',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'groups' => [
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipo de paquete eliminado',
                    'body'  => 'El tipo de paquete ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipo de paquete eliminado',
                    'body'  => 'El tipo de paquete ha sido eliminado exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información general',
                'entries' => [
                    'name'      => 'Nombre',
                    'fieldsets' => [
                        'size' => [
                            'title'   => 'Dimensiones del paquete',
                            'entries' => [
                                'length' => 'Largo',
                                'width'  => 'Ancho',
                                'height' => 'Alto',
                            ],
                        ],
                    ],
                    'weight'     => 'Peso base',
                    'max-weight' => 'Peso máximo',
                    'barcode'    => 'Código de barras',
                    'company'    => 'Empresa',
                    'created-at' => 'Creado el',
                    'updated-at' => 'Última actualización',
                ],
            ],

            'record-information' => [
                'title' => 'Información del registro',

                'entries' => [
                    'created-by'   => 'Creado por',
                    'created-at'   => 'Creado el',
                    'last-updated' => 'Última actualización',
                ],
            ],
        ],
    ],
];
