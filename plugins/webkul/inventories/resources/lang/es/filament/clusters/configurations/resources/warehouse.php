<?php

return [
    'navigation' => [
        'title' => 'Almacenes',
        'group' => 'Gestión de Almacenes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'name'               => 'Nombre',
                    'name-placeholder'   => 'ej. Almacén Central',
                    'code'               => 'Nombre Corto',
                    'code-placeholder'   => 'ej. AC',
                    'code-hint-tooltip'  => 'El nombre corto sirve como identificador del almacén.',
                    'company'            => 'Empresa',
                    'address'            => 'Dirección',
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',

                'fields' => [
                    'shipment-management'              => 'Gestión de Envíos',
                    'incoming-shipments'               => 'Envíos de Entrada',
                    'incoming-shipments-hint-tooltip'  => 'Ruta de entrada predeterminada a seguir',
                    'outgoing-shipments'               => 'Envíos de Salida',
                    'outgoing-shipments-hint-tooltip'  => 'Ruta de salida predeterminada a seguir',
                    'manufacture'                      => 'Fabricación',
                    'manufacture-hint-tooltip'         => 'Ruta de fabricación predeterminada a seguir',
                    'resupply-management'              => 'Gestión de Reabastecimiento',
                    'resupply-management-hint-tooltip' => 'Las rutas se generarán automáticamente para reabastecer este almacén desde los almacenes seleccionados.',
                    'resupply-from'                    => 'Reabastecer Desde',
                ],
            ],

            'additional' => [
                'title'  => 'Información Adicional',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'code'       => 'Nombre Corto',
            'company'    => 'Empresa',
            'address'    => 'Dirección',
            'deleted-at' => 'Eliminado el',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'groups' => [
            'address'       => 'Dirección',
            'company'       => 'Empresa',
            'created-at'    => 'Creado el',
            'updated-at'    => 'Actualizado el',
        ],

        'filters' => [
            'company' => 'Empresa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Almacén restaurado',
                    'body'  => 'El almacén ha sido restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Almacén eliminado',
                    'body'  => 'El almacén ha sido eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Almacén eliminado permanentemente',
                        'body'  => 'El almacén ha sido eliminado permanentemente de forma correcta.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el almacén',
                        'body'  => 'El almacén no puede eliminarse porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Almacenes restaurados',
                    'body'  => 'Los almacenes han sido restaurados correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Almacenes eliminados',
                    'body'  => 'Los almacenes han sido eliminados correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Almacenes eliminados permanentemente',
                        'body'  => 'Los almacenes han sido eliminados permanentemente de forma correcta.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los almacenes',
                        'body'  => 'Los almacenes no pueden eliminarse porque están en uso actualmente.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información General',

                'entries' => [
                    'name'    => 'Nombre del Almacén',
                    'code'    => 'Código del Almacén',
                    'company' => 'Empresa',
                    'address' => 'Dirección',
                ],
            ],

            'settings' => [
                'title' => 'Configuración',

                'entries' => [
                    'shipment-management' => 'Gestión de Envíos',
                    'incoming-shipments'  => 'Envíos de Entrada',
                    'outgoing-shipments'  => 'Envíos de Salida',
                    'resupply-management' => 'Gestión de Reabastecimiento',
                    'resupply-from'       => 'Reabastecer Desde',
                ],
            ],

            'record-information' => [
                'title' => 'Información del Registro',

                'entries' => [
                    'created-by'   => 'Creado por',
                    'created-at'   => 'Creado el',
                    'last-updated' => 'Última actualización',
                ],
            ],
        ],
    ],
];
