<?php

return [
    'navigation' => [
        'title' => 'Paquetes',
        'group' => 'Inventario',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'             => 'Nombre',
                    'name-placeholder' => 'ej. PAQ007',
                    'package-type'     => 'Tipo de Paquete',
                    'pack-date'        => 'Fecha de Empaquetado',
                    'location'         => 'Ubicación',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nombre',
            'package-type' => 'Tipo de Paquete',
            'location'     => 'Ubicación',
            'company'      => 'Empresa',
            'created-at'   => 'Creado En',
            'updated-at'   => 'Actualizado En',
        ],

        'groups' => [
            'package-type' => 'Tipo de Paquete',
            'location'     => 'Ubicación',
            'created-at'   => 'Creado En',
        ],

        'filters' => [
            'package-type' => 'Tipo de Paquete',
            'location'     => 'Ubicación',
            'creator'      => 'Creador',
            'company'      => 'Empresa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Paquete eliminado',
                        'body'  => 'El paquete ha sido eliminado exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el paquete',
                        'body'  => 'El paquete no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print-without-content' => [
                'label' => 'Imprimir Código de Barras',
            ],

            'print-with-content' => [
                'label' => 'Imprimir Código de Barras con Contenido',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Paquetes eliminados',
                        'body'  => 'Los paquetes han sido eliminados exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los paquetes',
                        'body'  => 'Los paquetes no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Detalles del Paquete',

                'entries' => [
                    'name'         => 'Nombre del Paquete',
                    'package-type' => 'Tipo de Paquete',
                    'pack-date'    => 'Fecha de Empaquetado',
                    'location'     => 'Ubicación',
                    'company'      => 'Empresa',
                    'created-at'   => 'Creado En',
                    'updated-at'   => 'Última Actualización',
                ],
            ],

            'record-information' => [
                'title' => 'Información del Registro',

                'entries' => [
                    'created-by'    => 'Creado Por',
                    'created-at'    => 'Creado En',
                    'last-updated'  => 'Última Actualización',
                ],
            ],
        ],
    ],
];
