<?php

return [
    'navigation' => [
        'title' => 'Ubicaciones',
        'group' => 'Gestión de almacenes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'location'                     => 'Ubicación',
                    'location-placeholder'         => 'p. ej. Existencias de Reserva',
                    'parent-location'              => 'Ubicación superior',
                    'parent-location-hint-tooltip' => 'La ubicación principal que engloba esta ubicación. Por ejemplo, la \'Zona de Despacho\' forma parte de la ubicación superior \'Puerta 1\'.',
                    'external-notes'               => 'Notas externas',
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',

                'fields' => [
                    'location-type'                 => 'Tipo de ubicación',
                    'company'                       => 'Empresa',
                    'storage-category'              => 'Categoría de almacenamiento',
                    'is-scrap'                      => '¿Es una Ubicación de Merma?',
                    'is-scrap-hint-tooltip'         => 'Selecciona esta casilla para designar esta ubicación para almacenar bienes dañados o de merma.',
                    'is-dock'                       => '¿Es una Ubicación de Muelle?',
                    'is-dock-hint-tooltip'          => 'Selecciona esta casilla para designar esta ubicación para almacenar bienes listos para envío.',
                    'is-replenish'                  => '¿Es una Ubicación de Reabastecimiento?',
                    'is-replenish-hint-tooltip'     => 'Activa esta función para recuperar todas las cantidades necesarias para el reabastecimiento en esta ubicación.',
                    'logistics'                     => 'Logística',
                    'removal-strategy'              => 'Estrategia de extracción',
                    'removal-strategy-hint-tooltip' => 'Especifica el método predeterminado para determinar el anaquel, lote y ubicación exactos de donde se tomarán los productos. Este método puede aplicarse a nivel de categoría de producto, con una alternativa de ubicaciones superiores si no se define aquí.',
                    'cyclic-counting'               => 'Conteo cíclico',
                    'inventory-frequency'           => 'Frecuencia de inventario',
                    'last-inventory'                => 'Último inventario',
                    'last-inventory-hint-tooltip'   => 'Fecha del último inventario en esta ubicación.',
                    'next-expected'                 => 'Próximo esperado',
                    'next-expected-hint-tooltip'    => 'Fecha del próximo inventario planificado según el calendario cíclico.',
                ],
            ],

            'additional' => [
                'title'  => 'Información adicional',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'location'         => 'Ubicación',
            'type'             => 'Tipo',
            'storage-category' => 'Categoría de almacenamiento',
            'company'          => 'Empresa',
            'deleted-at'       => 'Eliminado el',
            'created-at'       => 'Creado el',
            'updated-at'       => 'Actualizado el',
        ],

        'groups' => [
            'warehouse'       => 'Almacén',
            'type'            => 'Tipo',
            'created-at'      => 'Creado el',
            'updated-at'      => 'Actualizado el',
        ],

        'filters' => [
            'location' => 'Ubicación',
            'type'     => 'Tipo',
            'company'  => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Ubicación actualizada',
                    'body'  => 'La ubicación ha sido actualizada correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Ubicación restaurada',
                    'body'  => 'La ubicación ha sido restaurada correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ubicación eliminada',
                    'body'  => 'La ubicación ha sido eliminada correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ubicación eliminada permanentemente',
                        'body'  => 'La ubicación ha sido eliminada permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la ubicación',
                        'body'  => 'La ubicación no puede eliminarse porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimir Código de Barras',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Ubicaciones restauradas',
                    'body'  => 'Las ubicaciones han sido restauradas correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ubicaciones eliminadas',
                    'body'  => 'Las ubicaciones han sido eliminadas correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ubicaciones eliminadas permanentemente',
                        'body'  => 'Las ubicaciones han sido eliminadas permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las ubicaciones',
                        'body'  => 'Las ubicaciones no pueden eliminarse porque están en uso actualmente.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'entries' => [
                    'location'                     => 'Ubicación',
                    'location-placeholder'         => 'p. ej. Existencias de Reserva',
                    'parent-location'              => 'Ubicación superior',
                    'parent-location-hint-tooltip' => 'La ubicación principal que engloba esta ubicación. Por ejemplo, la \'Zona de Despacho\' forma parte de la ubicación superior \'Puerta 1\'.',
                    'external-notes'               => 'Notas externas',
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',

                'entries' => [
                    'location-type'                 => 'Tipo de ubicación',
                    'company'                       => 'Empresa',
                    'storage-category'              => 'Categoría de almacenamiento',
                    'is-scrap'                      => '¿Es una Ubicación de Merma?',
                    'is-scrap-hint-tooltip'         => 'Selecciona esta casilla para designar esta ubicación para almacenar bienes dañados o de merma.',
                    'is-dock'                       => '¿Es una Ubicación de Muelle?',
                    'is-dock-hint-tooltip'          => 'Selecciona esta casilla para designar esta ubicación para almacenar bienes listos para envío.',
                    'is-replenish'                  => '¿Es una Ubicación de Reabastecimiento?',
                    'is-replenish-hint-tooltip'     => 'Activa esta función para recuperar todas las cantidades necesarias para el reabastecimiento en esta ubicación.',
                    'logistics'                     => 'Logística',
                    'removal-strategy'              => 'Estrategia de extracción',
                    'removal-strategy-hint-tooltip' => 'Especifica el método predeterminado para determinar el anaquel, lote y ubicación exactos de donde se tomarán los productos. Este método puede aplicarse a nivel de categoría de producto, con una alternativa de ubicaciones superiores si no se define aquí.',
                    'cyclic-counting'               => 'Conteo cíclico',
                    'inventory-frequency'           => 'Frecuencia de inventario',
                    'last-inventory'                => 'Último inventario',
                    'last-inventory-hint-tooltip'   => 'Fecha del último inventario en esta ubicación.',
                    'next-expected'                 => 'Próximo esperado',
                    'next-expected-hint-tooltip'    => 'Fecha del próximo inventario planificado según el calendario cíclico.',
                ],
            ],

            'additional' => [
                'title'  => 'Información adicional',
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
