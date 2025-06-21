<?php

return [
    'navigation' => [
        'title' => 'Tipos de Operación',
        'group' => 'Gestión de Almacenes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'operator-type'             => 'Tipo de Operación',
                    'operator-type-placeholder' => 'ej. Recepciones',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Aplicable En',
                'description' => 'Selecciona los lugares donde esta ruta puede ser seleccionada.',

                'fields' => [
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'operator-type'                     => 'Tipo de Operación',
                    'sequence-prefix'                   => 'Prefijo de Secuencia',
                    'generate-shipping-labels'          => 'Generar Etiquetas de Envío',
                    'warehouse'                         => 'Almacén',
                    'show-reception-report'             => 'Mostrar Informe de Recepción al Validar',
                    'show-reception-report-hint-tooltip' => 'Si se selecciona, el sistema mostrará automáticamente el informe de recepción al validar, siempre que haya movimientos para asignar.',
                    'company'                           => 'Empresa',
                    'return-type'                       => 'Tipo de Devolución',
                    'create-backorder'                    => 'Crear Pedido Pendiente',
                    'move-type'                         => 'Tipo de Movimiento',
                    'move-type-hint-tooltip'            => 'A menos que lo defina el documento de origen, esta será la política de picking predeterminada para este tipo de operación.',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title'  => 'Lotes/Números de Serie',

                        'fields' => [
                            'create-new'                    => 'Crear Nuevo',
                            'create-new-hint-tooltip'       => 'Si se selecciona, el sistema asumirá que desea crear nuevos Lotes/Números de Serie, permitiéndole ingresarlos en un campo de texto.',
                            'use-existing'                  => 'Usar Existente',
                            'use-existing-hint-tooltip'     => 'Si se selecciona, puede elegir los Lotes/Números de Serie u optar por no asignar ninguno. Esto permite crear stock sin lote o sin restricciones en el lote utilizado.',
                        ],
                    ],

                    'locations' => [
                        'title'  => 'Ubicaciones',

                        'fields' => [
                            'source-location'                   => 'Ubicación de Origen',
                            'source-location-hint-tooltip'      => 'Esta sirve como la ubicación de origen predeterminada al crear manualmente esta operación. Sin embargo, se puede cambiar más tarde, y las rutas pueden asignar una ubicación predeterminada diferente.',
                            'destination-location'              => 'Ubicación de Destino',
                            'destination-location-hint-tooltip' => 'Esta es la ubicación de destino predeterminada para las operaciones creadas manualmente. Sin embargo, se puede modificar más tarde, y las rutas pueden asignar una ubicación predeterminada diferente.',
                        ],
                    ],

                    'packages' => [
                        'title'  => 'Paquetes',

                        'fields' => [
                            'show-entire-package'                   => 'Mover Paquete Completo',
                            'show-entire-package-hint-tooltip'      => 'Si se selecciona, puede mover paquetes completos.',
                        ],
                    ],
                ],
            ],

            'hardware' => [
                'title'  => 'Hardware',

                'fieldsets' => [
                    'print-on-validation' => [
                        'title'  => 'Imprimir al Validar',

                        'fields' => [
                            'delivery-slip'                     => 'Albarán de Entrega',
                            'delivery-slip-hint-tooltip'        => 'Si se selecciona, el sistema imprimirá automáticamente el albarán de entrega cuando se valide el picking.',

                            'return-slip'                       => 'Albarán de Devolución',
                            'return-slip-hint-tooltip'          => 'Si se selecciona, el sistema imprimirá automáticamente el albarán de devolución cuando se valide el picking.',

                            'product-labels'                    => 'Etiquetas de Producto',
                            'product-labels-hint-tooltip'       => 'Si se selecciona, el sistema imprimirá automáticamente las etiquetas de producto cuando se valide el picking.',

                            'lots-labels'                       => 'Etiquetas de Lote/NS',
                            'lots-labels-hint-tooltip'          => 'Si se selecciona, el sistema imprimirá automáticamente las etiquetas de lote/número de serie cuando se valide el picking.',

                            'reception-report'                  => 'Informe de Recepción',
                            'reception-report-hint-tooltip'     => 'Si se selecciona, el sistema imprimirá automáticamente el informe de recepción cuando se valide el picking y contenga movimientos asignados.',

                            'reception-report-labels'             => 'Etiquetas de Informe de Recepción',
                            'reception-report-labels-hint-tooltip' => 'Si se selecciona, el sistema imprimirá automáticamente las etiquetas del informe de recepción cuando se valide el picking.',

                            'package-content'                   => 'Contenido del Paquete',
                            'package-content-hint-tooltip'      => 'Si se selecciona, el sistema imprimirá automáticamente los detalles del paquete y su contenido cuando se valide el picking.',
                        ],
                    ],

                    'print-on-pack' => [
                        'title'  => 'Imprimir al "Poner en Paquete"',

                        'fields' => [
                            'package-label'                     => 'Etiqueta del Paquete',
                            'package-label-hint-tooltip'        => 'Si se selecciona, el sistema imprimirá automáticamente la etiqueta del paquete cuando se utilice el botón "Poner en Paquete".',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'warehouse'  => 'Almacén',
            'company'    => 'Empresa',
            'deleted-at' => 'Eliminado El',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'groups' => [
            'type'       => 'Tipo',
            'warehouse'  => 'Almacén',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'filters' => [
            'type'      => 'Tipo',
            'warehouse' => 'Almacén',
            'company'   => 'Empresa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de Operación restaurado',
                    'body'  => 'El tipo de operación ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de Operación eliminado',
                    'body'  => 'El tipo de operación ha sido eliminado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipo de Operación eliminado permanentemente',
                        'body'  => 'El tipo de operación ha sido eliminado permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el Tipo de Operación',
                        'body'  => 'El tipo de operación no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipos de Operación restaurados',
                    'body'  => 'Los tipos de operación han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipos de Operación eliminados',
                    'body'  => 'Los tipos de operación han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipos de Operación eliminados permanentemente',
                        'body'  => 'Los tipos de operación han sido eliminados permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los Tipos de Operación',
                        'body'  => 'Los tipos de operación no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],

        'empty-actions' => [
            'create' => [
                'label' => 'Crear Tipo de Operación',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información General',

                'entries' => [
                    'name' => 'Nombre',
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

        'tabs' => [
            'general' => [
                'title'   => 'General',

                'entries' => [
                    'type'                          => 'Tipo de Operación',
                    'sequence_code'                 => 'Código de Secuencia',
                    'print_label'                   => 'Imprimir Etiqueta',
                    'warehouse'                     => 'Almacén',
                    'reservation_method'            => 'Método de Reserva',
                    'auto_show_reception_report'    => 'Mostrar Informe de Recepción Automáticamente',
                    'company'                       => 'Empresa',
                    'return_operation_type'         => 'Tipo de Operación de Devolución',
                    'create_backorder'              => 'Crear Pedido Pendiente',
                    'move_type'                     => 'Tipo de Movimiento',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title'   => 'Lotes',

                        'entries' => [
                            'use_create_lots'   => 'Usar Crear Lotes',
                            'use_existing_lots' => 'Usar Lotes Existentes',
                        ],
                    ],

                    'locations' => [
                        'title'   => 'Ubicaciones',

                        'entries' => [
                            'source_location'      => 'Ubicación de Origen',
                            'destination_location' => 'Ubicación de Destino',
                        ],
                    ],
                ],
            ],
            'hardware' => [
                'title'   => 'Hardware',

                'fieldsets' => [
                    'print_on_validation' => [
                        'title'   => 'Imprimir al Validar',

                        'entries' => [
                            'auto_print_delivery_slip'          => 'Imprimir Albarán de Entrega Automáticamente',
                            'auto_print_return_slip'            => 'Imprimir Albarán de Devolución Automáticamente',
                            'auto_print_product_labels'         => 'Imprimir Etiquetas de Producto Automáticamente',
                            'auto_print_lot_labels'             => 'Imprimir Etiquetas de Lote Automáticamente',
                            'auto_print_reception_report'       => 'Imprimir Informe de Recepción Automáticamente',
                            'auto_print_reception_report_labels' => 'Imprimir Etiquetas de Informe de Recepción Automáticamente',
                            'auto_print_packages'               => 'Imprimir Paquetes Automáticamente',
                        ],
                    ],

                    'print_on_pack' => [
                        'title'   => 'Imprimir al Empaquetar',

                        'entries' => [
                            'auto_print_package_label' => 'Imprimir Etiqueta de Paquete Automáticamente',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
