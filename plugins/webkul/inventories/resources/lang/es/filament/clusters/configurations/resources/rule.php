<?php

return [
    'navigation' => [
        'title' => 'Reglas',
        'group' => 'Gestión de Almacenes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name'                          => 'Nombre',
                    'action'                        => 'Acción',
                    'operation-type'                => 'Tipo de Operación',
                    'source-location'               => 'Ubicación de Origen',
                    'destination-location'          => 'Ubicación de Destino',
                    'supply-method'                 => 'Método de Suministro',
                    'supply-method-hint-tooltip'    => 'Tomar del Stock: Los productos se obtienen directamente del stock disponible en la ubicación de origen.<br/>Activar Otra Regla: El sistema ignora el stock disponible y busca una regla de stock para reponer la ubicación de origen.<br/>Tomar del Stock, si no está Disponible, Activar Otra Regla: Los productos se toman primero del stock disponible. Si no hay ninguno disponible, el sistema aplica una regla de stock para llevar los productos a la ubicación de origen.',
                    'automatic-move'                => 'Movimiento Automático',
                    'automatic-move-hint-tooltip'   => 'Operación Manual: Crea un movimiento de stock separado después del actual.<br/>Automático Sin Paso Adicional: Reemplaza directamente la ubicación en el movimiento original sin agregar un paso adicional.',

                    'action-information' => [
                        'pull' => 'Cuando se requieren productos en <b>:sourceLocation</b>, se genera <b>:operation</b> desde <b>:destinationLocation</b> para satisfacer la demanda.',
                        'push' => 'Cuando los productos llegan a <b>:sourceLocation</b>,</br>se genera <b>:operation</b> para transferirlos a <b>:destinationLocation</b>.',
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',

                'fields' => [
                    'partner-address'               => 'Dirección del Partner',
                    'partner-address-hint-tooltip'  => 'Dirección donde se deben entregar los bienes. Opcional.',
                    'lead-time'                     => 'Plazo de Entrega (Días)',
                    'lead-time-hint-tooltip'        => 'La fecha de transferencia esperada se calculará utilizando este plazo de entrega.',
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
                            'propagation-procurement-group'             => 'Propagación del Grupo de Adquisición',
                            'propagation-procurement-group-hint-tooltip' => 'Si se selecciona, cancelar el movimiento creado por esta regla también cancelará el movimiento posterior.',
                            'cancel-next-move'                          => 'Cancelar el Próximo Movimiento',
                            'warehouse-to-propagate'                    => 'Almacén a Propagar',
                            'warehouse-to-propagate-hint-tooltip'       => 'El almacén asignado al movimiento o adquisición creado, que puede diferir del almacén al que se aplica esta regla (por ejemplo, para reglas de reabastecimiento desde otro almacén).',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nombre',
            'action'             => 'Acción',
            'source-location'    => 'Ubicación de Origen',
            'destination-location' => 'Ubicación de Destino',
            'route'              => 'Ruta',
            'deleted-at'         => 'Eliminado El',
            'created-at'         => 'Creado El',
            'updated-at'         => 'Actualizado El',
        ],

        'groups' => [
            'action'             => 'Acción',
            'source-location'    => 'Ubicación de Origen',
            'destination-location' => 'Ubicación de Destino',
            'route'              => 'Ruta',
            'created-at'         => 'Creado El',
            'updated-at'         => 'Actualizado El',
        ],

        'filters' => [
            'action'             => 'Acción',
            'source-location'    => 'Ubicación de Origen',
            'destination-location' => 'Ubicación de Destino',
            'route'              => 'Ruta',
            'company'            => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Regla actualizada',
                    'body'  => 'La regla ha sido actualizada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Regla restaurada',
                    'body'  => 'La regla ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Regla eliminada',
                    'body'  => 'La regla ha sido eliminada exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Regla eliminada permanentemente',
                        'body'  => 'La regla ha sido eliminada permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la regla',
                        'body'  => 'La regla no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Reglas restauradas',
                    'body'  => 'Las reglas han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Reglas eliminadas',
                    'body'  => 'Las reglas han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Reglas eliminadas permanentemente',
                        'body'  => 'Las reglas han sido eliminadas permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las reglas',
                        'body'  => 'Las reglas no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'       => 'Detalles de la Regla',
                'description' => [
                    'pull' => 'Cuando se requieren productos en <b>:sourceLocation</b>, se genera <b>:operation</b> desde <b>:destinationLocation</b> para satisfacer la demanda.',
                    'push' => 'Cuando los productos llegan a <b>:sourceLocation</b>, se genera <b>:operation</b> para transferirlos a <b>:destinationLocation</b>.',
                ],
                'entries' => [
                    'name'               => 'Nombre de la Regla',
                    'action'             => 'Acción',
                    'operation-type'     => 'Tipo de Operación',
                    'source-location'    => 'Ubicación de Origen',
                    'destination-location' => 'Ubicación de Destino',
                    'route'              => 'Ruta',
                    'company'            => 'Empresa',
                    'partner-address'    => 'Dirección del Partner',
                    'lead-time'          => 'Plazo de Entrega',
                    'action-information' => 'Información de la Acción',
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