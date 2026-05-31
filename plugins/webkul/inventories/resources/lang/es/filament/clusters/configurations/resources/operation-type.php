<?php

return [
    'navigation' => [
        'title' => 'Tipos de operación',
        'group' => 'Gestión de almacenes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'operator-type'             => 'Tipo de operador',
                    'operator-type-placeholder' => 'p. ej. Recepciones',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Aplicable a',
                'description' => 'Seleccione los lugares donde se puede elegir esta ruta.',

                'fields' => [
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'operator-type'                      => 'Tipo de operador',
                    'sequence-prefix'                    => 'Prefijo de secuencia',
                    'generate-shipping-labels'           => 'Generar etiquetas de envío',
                    'warehouse'                          => 'Almacén',
                    'show-reception-report'              => 'Mostrar reporte de recepción al validar',
                    'show-reception-report-hint-tooltip' => 'Si se selecciona, el sistema mostrará automáticamente el reporte de recepción al validar, siempre que haya movimientos por asignar.',
                    'company'                            => 'Empresa',
                    'return-type'                        => 'Tipo de devolución',
                    'create-backorder'                   => 'Crear pedido pendiente',
                    'move-type'                          => 'Tipo de movimiento',
                    'move-type-hint-tooltip'             => 'A menos que lo defina el documento origen, esto servirá como política de recolección predeterminada para este tipo de operación.',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title'  => 'Lotes/números de serie',

                        'fields' => [
                            'create-new'                => 'Crear nuevo',
                            'create-new-hint-tooltip'   => 'Si se selecciona, el sistema asumirá que desea crear nuevos lotes/números de serie, permitiéndole ingresarlos en un campo de texto.',
                            'use-existing'              => 'Usar existente',
                            'use-existing-hint-tooltip' => 'Si se selecciona, puede elegir los lotes/números de serie u optar por no asignar ninguno. Esto permite crear existencias sin un lote o sin restricciones sobre el lote utilizado.',
                        ],
                    ],

                    'locations' => [
                        'title'  => 'Ubicaciones',

                        'fields' => [
                            'source-location'                   => 'Ubicación de origen',
                            'source-location-hint-tooltip'      => 'Esta es la ubicación de origen predeterminada al crear manualmente esta operación. Sin embargo, puede cambiarse posteriormente y las rutas pueden asignar una ubicación predeterminada diferente.',
                            'destination-location'              => 'Ubicación de destino',
                            'destination-location-hint-tooltip' => 'Esta es la ubicación de destino predeterminada para operaciones creadas manualmente. Sin embargo, puede modificarse posteriormente y las rutas pueden asignar una ubicación predeterminada diferente.',
                        ],
                    ],

                    'packages' => [
                        'title'  => 'Paquetes',

                        'fields' => [
                            'show-entire-package'              => 'Mover paquete completo',
                            'show-entire-package-hint-tooltip' => 'Si se selecciona, puede mover paquetes completos.',
                        ],
                    ],
                ],
            ],

            'hardware' => [
                'title'  => 'Hardware',

                'fieldsets' => [
                    'print-on-validation' => [
                        'title'  => 'Imprimir al validar',

                        'fields' => [
                            'delivery-slip'              => 'Comprobante de entrega',
                            'delivery-slip-hint-tooltip' => 'Si se selecciona, el sistema imprimirá automáticamente el comprobante de entrega cuando se valide la recolección.',

                            'return-slip'              => 'Comprobante de devolución',
                            'return-slip-hint-tooltip' => 'Si se selecciona, el sistema imprimirá automáticamente el comprobante de devolución cuando se valide la recolección.',

                            'product-labels'              => 'Etiquetas de producto',
                            'product-labels-hint-tooltip' => 'Si se selecciona, el sistema imprimirá automáticamente las etiquetas de producto cuando se valide la recolección.',

                            'lots-labels'              => 'Etiquetas de lote/NS',
                            'lots-labels-hint-tooltip' => 'Si se selecciona, el sistema imprimirá automáticamente las etiquetas de lote/número de serie cuando se valide la recolección.',

                            'reception-report'              => 'Reporte de recepción',
                            'reception-report-hint-tooltip' => 'Si se selecciona, el sistema imprimirá automáticamente el reporte de recepción cuando se valide la recolección y contenga movimientos asignados.',

                            'reception-report-labels'              => 'Etiquetas del reporte de recepción',
                            'reception-report-labels-hint-tooltip' => 'Si se selecciona, el sistema imprimirá automáticamente las etiquetas del reporte de recepción cuando se valide la recolección.',

                            'package-content'              => 'Contenido del paquete',
                            'package-content-hint-tooltip' => 'Si se selecciona, el sistema imprimirá automáticamente los detalles del paquete y su contenido cuando se valide la recolección.',
                        ],
                    ],

                    'print-on-pack' => [
                        'title'  => 'Imprimir al "Empacar"',

                        'fields' => [
                            'package-label'              => 'Etiqueta de paquete',
                            'package-label-hint-tooltip' => 'Si se selecciona, el sistema imprimirá automáticamente la etiqueta del paquete cuando se use el botón "Empacar".',
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
            'deleted-at' => 'Eliminado el',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'groups' => [
            'type'       => 'Tipo',
            'warehouse'  => 'Almacén',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'type'      => 'Tipo',
            'warehouse' => 'Almacén',
            'company'   => 'Empresa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de operación restaurado',
                    'body'  => 'El tipo de operación ha sido restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de operación eliminado',
                    'body'  => 'El tipo de operación ha sido eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipo de operación eliminado permanentemente',
                        'body'  => 'El tipo de operación ha sido eliminado permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el tipo de operación',
                        'body'  => 'El tipo de operación no puede eliminarse porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipos de operación restaurados',
                    'body'  => 'Los tipos de operación han sido restaurados correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipos de operación eliminados',
                    'body'  => 'Los tipos de operación han sido eliminados correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipos de operación eliminados permanentemente',
                        'body'  => 'Los tipos de operación han sido eliminados permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los tipos de operación',
                        'body'  => 'Los tipos de operación no pueden eliminarse porque están en uso actualmente.',
                    ],
                ],
            ],
        ],

        'empty-actions' => [
            'create' => [
                'label' => 'Crear tipo de operación',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información general',

                'entries' => [
                    'name' => 'Nombre',
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

        'tabs' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'type'                       => 'Tipo de operación',
                    'sequence_code'              => 'Código de secuencia',
                    'print_label'                => 'Imprimir etiqueta',
                    'warehouse'                  => 'Almacén',
                    'reservation_method'         => 'Método de reserva',
                    'auto_show_reception_report' => 'Mostrar reporte de recepción automáticamente',
                    'company'                    => 'Empresa',
                    'return_operation_type'      => 'Tipo de operación de devolución',
                    'create_backorder'           => 'Crear pedido pendiente',
                    'move_type'                  => 'Tipo de movimiento',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title' => 'Lotes',

                        'entries' => [
                            'use_create_lots'   => 'Crear lotes',
                            'use_existing_lots' => 'Usar lotes existentes',
                        ],
                    ],

                    'locations' => [
                        'title' => 'Ubicaciones',

                        'entries' => [
                            'source_location'      => 'Ubicación de origen',
                            'destination_location' => 'Ubicación de destino',
                        ],
                    ],
                ],
            ],
            'hardware' => [
                'title' => 'Hardware',

                'fieldsets' => [
                    'print_on_validation' => [
                        'title' => 'Imprimir al validar',

                        'entries' => [
                            'auto_print_delivery_slip'           => 'Imprimir comprobante de entrega automáticamente',
                            'auto_print_return_slip'             => 'Imprimir comprobante de devolución automáticamente',
                            'auto_print_product_labels'          => 'Imprimir etiquetas de producto automáticamente',
                            'auto_print_lot_labels'              => 'Imprimir etiquetas de lote automáticamente',
                            'auto_print_reception_report'        => 'Imprimir reporte de recepción automáticamente',
                            'auto_print_reception_report_labels' => 'Imprimir etiquetas del reporte de recepción automáticamente',
                            'auto_print_packages'                => 'Imprimir paquetes automáticamente',
                        ],
                    ],

                    'print_on_pack' => [
                        'title' => 'Imprimir al empacar',

                        'entries' => [
                            'auto_print_package_label' => 'Imprimir etiqueta de paquete automáticamente',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
