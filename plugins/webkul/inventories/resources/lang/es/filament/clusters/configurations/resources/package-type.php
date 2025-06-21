<?php

return [
    'navigation' => [
        'title' => 'Tipos de Paquete',
        'group' => 'Entrega',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name'       => 'Nombre',
                    'barcode'    => 'Código de Barras',
                    'company'    => 'Empresa',
                    'weight'     => 'Peso',
                    'max-weight' => 'Peso Máximo',

                    'fieldsets' => [
                        'size' => [
                            'title'  => 'Tamaño',

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
            'barcode'    => 'Código de Barras',
            'weight'     => 'Peso',
            'max-weight' => 'Peso Máximo',
            'width'      => 'Ancho',
            'height'     => 'Alto',
            'length'     => 'Largo',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'groups' => [
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipo de Paquete eliminado',
                    'body'  => 'El tipo de paquete ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipo de Paquete eliminado',
                    'body'  => 'El tipo de paquete ha sido eliminado exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información General',
                'entries' => [
                    'name'      => 'Nombre',
                    'fieldsets' => [
                        'size' => [
                            'title'   => 'Dimensiones del Paquete',
                            'entries' => [
                                'length' => 'Largo',
                                'width'  => 'Ancho',
                                'height' => 'Alto',
                            ],
                        ],
                    ],
                    'weight'     => 'Peso Base',
                    'max-weight' => 'Peso Máximo',
                    'barcode'    => 'Código de Barras',
                    'company'    => 'Empresa',
                    'created-at' => 'Creado El',
                    'updated-at' => 'Actualizado El',
                ],
            ],

            'record-information' => [
                'title'   => 'Información del Registro',
                'entries' => [
                    'created-by'   => 'Creado Por',
                    'created-at'   => 'Creado El',
                    'last-updated' => 'Última Actualización',
                ],
            ],
        ],
    ],
];
