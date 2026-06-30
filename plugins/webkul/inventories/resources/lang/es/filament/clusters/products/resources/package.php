<?php

return [
    'navigation' => [
        'title' => 'Paquetes',
        'group' => 'Inventario',
    ],

    'global-search' => [
        'name'         => 'Nombre',
        'package-type' => 'Tipo de paquete',
        'location'     => 'Ubicación',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'             => 'Nombre',
                    'name-placeholder' => 'p. ej. PACK007',
                    'package-type'     => 'Tipo de paquete',
                    'pack-date'        => 'Fecha de empaque',
                    'location'         => 'Ubicación',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nombre',
            'package-type' => 'Tipo de paquete',
            'location'     => 'Ubicación',
            'company'      => 'Empresa',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'groups' => [
            'package-type'   => 'Tipo de paquete',
            'location'       => 'Ubicación',
            'created-at'     => 'Creado el',
        ],

        'filters' => [
            'package-type' => 'Tipo de paquete',
            'location'     => 'Ubicación',
            'creator'      => 'Creador',
            'company'      => 'Empresa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Paquete eliminado',
                        'body'  => 'El paquete ha sido eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el paquete',
                        'body'  => 'El paquete no puede eliminarse porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print-without-content' => [
                'label' => 'Imprimir código de barras',
            ],

            'print-with-content' => [
                'label' => 'Imprimir código de barras con contenido',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Paquetes eliminados',
                        'body'  => 'Los paquetes han sido eliminados correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los paquetes',
                        'body'  => 'Los paquetes no pueden eliminarse porque están en uso actualmente.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Detalles del paquete',

                'entries' => [
                    'name'         => 'Nombre del paquete',
                    'package-type' => 'Tipo de paquete',
                    'pack-date'    => 'Fecha de empaque',
                    'location'     => 'Ubicación',
                    'company'      => 'Empresa',
                    'created-at'   => 'Creado el',
                    'updated-at'   => 'Última actualización',
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
