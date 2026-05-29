<?php

return [
    'navigation' => [
        'title' => 'Reglas',
        'group' => 'Gestión de almacenes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name'                        => 'Nombre',
                    'action'                      => 'Acción',
                    'operation-type'              => 'Tipo de operación',
                    'source-location'             => 'Ubicación de origen',
                    'destination-location'        => 'Ubicación de destino',
                    'supply-method'               => 'Método de suministro',
                    'supply-method-hint-tooltip'  => 'Tomar de existencias: Los productos se obtienen directamente del stock disponible en la ubicación de origen.<br/>Activar otra regla: El sistema ignora el stock disponible y busca una regla de existencias para reabastecer la ubicación de origen.<br/>Tomar de existencias; si no está disponible, activar otra regla: Primero se toman los productos del stock disponible. Si no hay ninguno disponible, el sistema aplica una regla de existencias para traer productos a la ubicación de origen.',
                    'automatic-move'              => 'Movimiento automático',
                    'automatic-move-hint-tooltip' => 'Operación manual: Crea un movimiento de existencias separado después del actual.<br/>Automático sin paso adicional: Reemplaza directamente la ubicación en el movimiento original sin agregar un paso extra.',

                    'action-information' => [
                        'pull'        => 'Cuando se requieren productos en <b>:sourceLocation</b>, :operation se genera desde <b>:destinationLocation</b> para satisfacer la demanda.',
                        'push'        => 'Cuando los productos llegan a <b>:sourceLocation</b>,</br><b>:operation</b> se genera para transferirlos a <b>:destinationLocation</b>.',
                        'buy'         => 'Cuando se necesitan productos en <b>:destinationLocation</b>, se crea una solicitud de cotización para satisfacer la necesidad.',
                        'manufacture' => 'Cuando se necesitan productos en <b>:destinationLocation</b>, se crea una orden de fabricación para satisfacer la necesidad.',
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',

                'fields' => [
                    'partner-address'              => 'Dirección del contacto',
                    'partner-address-hint-tooltip' => 'Dirección donde deben entregarse los bienes. Opcional.',
                    'lead-time'                    => 'Tiempo de entrega (días)',
                    'lead-time-hint-tooltip'       => 'La fecha de transferencia esperada se calculará usando este tiempo de entrega.',
                ],

                'fieldsets' => [
                    'applicability' => [
                        'title'  => 'Aplicabilidad',

                        'fields' => [
                            'route'   => 'Ruta',
                            'company' => 'Empresa',
                        ],
                    ],

                    'propagation' => [
                        'title'  => 'Propagación',

                        'fields' => [
                            'propagation-procurement-group'              => 'Propagación del grupo de aprovisionamiento',
                            'propagation-procurement-group-hint-tooltip' => 'Si se selecciona, cancelar el movimiento creado por esta regla también cancelará el movimiento siguiente.',
                            'cancel-next-move'                           => 'Cancelar el siguiente movimiento',
                            'warehouse-to-propagate'                     => 'Almacén a propagar',
                            'warehouse-to-propagate-hint-tooltip'        => 'El almacén asignado al movimiento o aprovisionamiento creado, que puede diferir del almacén al que se aplica esta regla (p. ej., para reglas de reabastecimiento desde otro almacén).',
                        ],
                    ],
                ],

            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Nombre',
            'action'               => 'Acción',
            'source-location'      => 'Ubicación de origen',
            'destination-location' => 'Ubicación de destino',
            'route'                => 'Ruta',
            'deleted-at'           => 'Eliminado el',
            'created-at'           => 'Creado el',
            'updated-at'           => 'Actualizado el',
        ],

        'groups' => [
            'action'               => 'Acción',
            'source-location'      => 'Ubicación de origen',
            'destination-location' => 'Ubicación de destino',
            'route'                => 'Ruta',
            'created-at'           => 'Creado el',
            'updated-at'           => 'Actualizado el',
        ],

        'filters' => [
            'action'               => 'Acción',
            'source-location'      => 'Ubicación de origen',
            'destination-location' => 'Ubicación de destino',
            'route'                => 'Ruta',
            'company'              => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Regla actualizada',
                    'body'  => 'La regla ha sido actualizada correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Regla restaurada',
                    'body'  => 'La regla ha sido restaurada correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Regla eliminada',
                    'body'  => 'La regla ha sido eliminada correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Regla eliminada de forma permanente',
                        'body'  => 'La regla ha sido eliminada de forma permanente correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la regla',
                        'body'  => 'La regla no puede eliminarse porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Reglas restauradas',
                    'body'  => 'Las reglas han sido restauradas correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Reglas eliminadas',
                    'body'  => 'Las reglas han sido eliminadas correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Reglas eliminadas de forma permanente',
                        'body'  => 'Las reglas han sido eliminadas de forma permanente correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las reglas',
                        'body'  => 'Las reglas no pueden eliminarse porque están en uso actualmente.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Detalles de la regla',

                'description' => [
                    'pull' => 'Cuando se requieren productos en <b>:sourceLocation</b>, <b>:operation</b> se genera desde <b>:destinationLocation</b> para satisfacer la demanda.',
                    'push' => 'Cuando los productos llegan a <b>:sourceLocation</b>, <b>:operation</b> se genera para transferirlos a <b>:destinationLocation</b>.',
                ],

                'entries' => [
                    'name'                 => 'Nombre de la regla',
                    'action'               => 'Acción',
                    'operation-type'       => 'Tipo de operación',
                    'source-location'      => 'Ubicación de origen',
                    'destination-location' => 'Ubicación de destino',
                    'route'                => 'Ruta',
                    'company'              => 'Empresa',
                    'partner-address'      => 'Dirección del contacto',
                    'lead-time'            => 'Tiempo de entrega',
                    'action-information'   => 'Información de acción',
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
