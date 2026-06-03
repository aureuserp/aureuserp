<?php

return [
    'navigation' => [
        'title' => 'Listas de materiales',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'reference'             => 'Referencia',
                    'reference-placeholder' => 'ej. LdM-001',
                    'product'               => 'Producto',
                    'product-variant'       => 'Variante del producto',
                    'quantity'              => 'Cantidad',
                    'uom'                   => 'UOM',
                    'operation-type'        => 'Tipo de operación',
                    'company'               => 'Empresa',
                    'type'                  => 'Tipo de LdM',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Varios',
                'fields' => [
                    'kit-information'                         => 'Información del kit',
                    'kit-information-content'                 => 'Una LdM de tipo kit se usa para agrupar componentes en transferencias o ventas, en lugar de producirse mediante una orden de fabricación.',
                    'manufacturing-lead-time'                 => 'Tiempo de entrega de fabricación',
                    'days-to-prepare-manufacturing-order'     => 'Días para preparar la orden de fabricación',
                    'days-suffix'                             => 'días',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'      => 'Componentes',
                'add-action' => 'Agregar una línea',
                'columns'    => [
                    'component'              => 'Componente',
                    'apply-on-variants'      => 'Aplicar en variantes',
                    'consumed-in-operation'  => 'Consumido en operación',
                    'highlight-consumption'  => 'Resaltar consumo',
                    'quantity'               => 'Cantidad',
                    'uom'                    => 'Unidad de medida del producto',
                ],
                'create-form' => [
                    'fields' => [
                        'name'            => 'Nombre',
                        'type'            => 'Tipo',
                        'category'        => 'Categoría',
                        'company'         => 'Empresa',
                        'uom'             => 'UOM',
                        'uom-placeholder' => 'UOM',
                    ],
                ],
            ],
            'operations' => [
                'title'      => 'Operaciones',
                'add-action' => 'Agregar una línea',
                'actions'    => [
                    'edit'                 => 'Editar operación',
                    'copy-existing'        => 'Copiar operaciones existentes',
                    'copy-existing-fields' => [
                        'operation' => 'Operación',
                    ],
                ],
                'columns'    => [
                    'operation'        => 'Operación',
                    'work-center'      => 'Centro de trabajo',
                    'time-mode'        => 'Cálculo de duración',
                    'time-mode-batch'  => 'Calculado en el último',
                    'company'          => 'Empresa',
                    'apply-on-variants'=> 'Aplicar en variantes',
                    'duration'         => 'Duración (minutos)',
                ],
            ],
            'by-products' => [
                'title'      => 'Subproductos',
                'add-action' => 'Agregar una línea',
                'columns'    => [
                    'product'   => 'Subproducto',
                    'quantity'  => 'Cantidad',
                    'uom'       => 'Unidad de medida',
                    'operation' => 'Producido en operación',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Varios',
                'fields' => [
                    'ready-to-produce'       => 'Preparación para fabricación',
                    'routing'                => 'Ruta de fabricación',
                    'consumption'            => 'Consumo flexible',
                    'operation-dependencies' => 'Dependencias de operaciones',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'  => 'Referencia',
            'product'    => 'Producto',
            'quantity'   => 'Cantidad',
            'uom'        => 'UOM',
            'type'       => 'Tipo de LdM',
            'company'    => 'Empresa',
            'deleted-at' => 'Eliminado el',
            'updated-at' => 'Actualizado el',
        ],
        'filters' => [
            'product' => 'Producto',
            'type'    => 'Tipo de LdM',
            'company' => 'Empresa',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Lista de materiales restaurada',
                    'body'  => 'La lista de materiales ha sido restaurada correctamente.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Lista de materiales archivada',
                    'body'  => 'La lista de materiales ha sido archivada correctamente.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lista de materiales eliminada',
                        'body'  => 'La lista de materiales ha sido eliminada permanentemente.',
                    ],
                    'error' => [
                        'title' => 'No se pudo eliminar la lista de materiales',
                        'body'  => 'La lista de materiales no puede eliminarse porque está en uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Listas de materiales restauradas',
                    'body'  => 'Las listas de materiales seleccionadas han sido restauradas correctamente.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Listas de materiales archivadas',
                    'body'  => 'Las listas de materiales seleccionadas han sido archivadas correctamente.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Listas de materiales eliminadas',
                        'body'  => 'Las listas de materiales seleccionadas han sido eliminadas permanentemente.',
                    ],
                    'error' => [
                        'title' => 'No se pudieron eliminar las listas de materiales',
                        'body'  => 'Una o más listas de materiales seleccionadas están en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información general',
                'entries' => [
                    'reference'      => 'Referencia',
                    'product'        => 'Producto',
                    'product-variant'=> 'Variante del producto',
                    'quantity'       => 'Cantidad',
                    'uom'            => 'UOM',
                    'operation-type' => 'Tipo de operación',
                    'company'        => 'Empresa',
                    'type'           => 'Tipo de LdM',
                ],
            ],
            'record-information' => [
                'title'   => 'Información del registro',
                'entries' => [
                    'created-by'   => 'Creado por',
                    'created-at'   => 'Creado el',
                    'last-updated' => 'Última actualización',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'   => 'Componentes',
                'entries' => [
                    'component' => 'Componente',
                    'operation' => 'Operación',
                    'quantity'  => 'Cantidad',
                    'uom'       => 'Unidad de medida del producto',
                ],
            ],
            'operations' => [
                'title'   => 'Operaciones',
                'entries' => [
                    'operation'   => 'Operación',
                    'work-center' => 'Centro de trabajo',
                    'time-mode'   => 'Cálculo de duración',
                    'duration'    => 'Duración (minutos)',
                ],
            ],
            'by-products' => [
                'title'   => 'Subproductos',
                'entries' => [
                    'product'   => 'Subproducto',
                    'quantity'  => 'Cantidad',
                    'uom'       => 'Unidad de medida',
                    'operation' => 'Producido en operación',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Varios',
                'entries' => [
                    'kit-information'                         => 'Información del kit',
                    'kit-information-content'                 => 'Una LdM de tipo kit se usa para agrupar componentes en transferencias o ventas, en lugar de producirse mediante una orden de fabricación.',
                    'ready-to-produce'                        => 'Preparación para fabricación',
                    'routing'                                 => 'Ruta de fabricación',
                    'consumption'                             => 'Consumo flexible',
                    'operation-dependencies'                  => 'Dependencias de operaciones',
                    'manufacturing-lead-time'                 => 'Tiempo de entrega de fabricación',
                    'days-to-prepare-manufacturing-order'     => 'Días para preparar la orden de fabricación',
                    'days-suffix'                             => 'días',
                ],
            ],
        ],
    ],
];
