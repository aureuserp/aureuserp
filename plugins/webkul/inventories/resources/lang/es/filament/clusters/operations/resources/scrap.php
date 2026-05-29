<?php

return [
    'navigation' => [
        'title' => 'Mermas',
        'group' => 'Ajustes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'product'              => 'Producto',
                    'package'              => 'Paquete',
                    'quantity'             => 'Cantidad',
                    'unit'                 => 'Unidad de medida',
                    'lot'                  => 'Lote/NS',
                    'tags'                 => 'Etiquetas',
                    'name'                 => 'Nombre',
                    'color'                => 'Color',
                    'owner'                => 'Propietario',
                    'source-location'      => 'Ubicación de origen',
                    'destination-location' => 'Ubicación de merma',
                    'source-document'      => 'Documento de origen',
                    'company'              => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Fecha',
            'reference'       => 'Referencia',
            'product'         => 'Producto',
            'package'         => 'Paquete',
            'quantity'        => 'Cantidad',
            'uom'             => 'Unidad de medida',
            'source-location' => 'Ubicación de origen',
            'scrap-location'  => 'Ubicación de merma',
            'unit'            => 'Unidad de medida',
            'lot'             => 'Lote/NS',
            'tags'            => 'Etiquetas',
            'state'           => 'Estado',
        ],

        'groups' => [
            'product'              => 'Producto',
            'source-location'      => 'Ubicación de origen',
            'destination-location' => 'Ubicación de merma',
        ],

        'filters' => [
            'source-location'      => 'Ubicación de origen',
            'destination-location' => 'Ubicación de merma',
            'product'              => 'Producto',
            'state'                => 'Estado',
            'product-category'     => 'Categoría de producto',
            'uom'                  => 'Unidad de medida',
            'lot'                  => 'Lote/NS',
            'package'              => 'Paquete',
            'tags'                 => 'Etiquetas',
            'company'              => 'Empresa',
            'quantity'             => 'Cantidad',
            'creator'              => 'Creador',
            'closed-at'            => 'Cerrado el',
            'created-at'           => 'Creado el',
            'updated-at'           => 'Actualizado el',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Merma eliminada',
                        'body'  => 'La merma ha sido eliminada exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la merma',
                        'body'  => 'La merma no puede eliminarse porque está en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Mermas eliminadas',
                        'body'  => 'Las mermas seleccionadas han sido eliminadas exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las mermas',
                        'body'  => 'Las mermas no pueden eliminarse porque están en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Detalles de la merma',

                'entries' => [
                    'product'              => 'Producto',
                    'quantity'             => 'Cantidad',
                    'lot'                  => 'Lote',
                    'tags'                 => 'Etiquetas',
                    'package'              => 'Paquete',
                    'owner'                => 'Propietario',
                    'source-location'      => 'Ubicación de origen',
                    'destination-location' => 'Ubicación de destino',
                    'source-document'      => 'Documento de origen',
                    'company'              => 'Empresa',
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
