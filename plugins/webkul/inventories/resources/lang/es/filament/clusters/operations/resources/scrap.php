<?php

return [
    'navigation' => [
        'title' => 'Desechos',
        'group' => 'Ajustes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'product'               => 'Producto',
                    'package'               => 'Paquete',
                    'quantity'              => 'Cantidad',
                    'unit'                  => 'Unidad de Medida',
                    'lot'                   => 'Lote/Serie',
                    'tags'                  => 'Etiquetas',
                    'name'                  => 'Nombre',
                    'color'                 => 'Color',
                    'owner'                 => 'Propietario',
                    'source-location'       => 'Ubicación de Origen',
                    'destination-location'  => 'Ubicación de Desecho',
                    'source-document'       => 'Documento de Origen',
                    'company'               => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'date'              => 'Fecha',
            'reference'         => 'Referencia',
            'product'           => 'Producto',
            'package'           => 'Paquete',
            'quantity'          => 'Cantidad',
            'uom'               => 'Unidad de Medida',
            'source-location'   => 'Ubicación de Origen',
            'scrap-location'    => 'Ubicación de Desecho',
            'unit'              => 'Unidad de Medida', // Duplicado, revisar si es intencional
            'lot'               => 'Lote/Serie',
            'tags'              => 'Etiquetas',
            'state'             => 'Estado',
        ],

        'groups' => [
            'product'           => 'Producto',
            'source-location'   => 'Ubicación de Origen',
            'destination-location' => 'Ubicación de Desecho',
        ],

        'filters' => [
            'source-location'       => 'Ubicación de Origen',
            'destination-location'  => 'Ubicación de Desecho',
            'product'               => 'Producto',
            'state'                 => 'Estado',
            'product-category'      => 'Categoría de Producto',
            'uom'                   => 'Unidad de Medida',
            'lot'                   => 'Lote/Serie',
            'package'               => 'Paquete',
            'tags'                  => 'Etiquetas',
            'company'               => 'Empresa',
            'quantity'              => 'Cantidad',
            'creator'               => 'Creador',
            'closed-at'             => 'Cerrado En',
            'created-at'            => 'Creado En',
            'updated-at'            => 'Actualizado En',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Desecho eliminado',
                        'body'  => 'El desecho ha sido eliminado exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el desecho',
                        'body'  => 'El desecho no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Desechos eliminados',
                        'body'  => 'Los desechos seleccionados han sido eliminados exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los desechos',
                        'body'  => 'Los desechos no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Detalles del Desecho',

                'entries' => [
                    'product'               => 'Producto',
                    'quantity'              => 'Cantidad',
                    'lot'                   => 'Lote',
                    'tags'                  => 'Etiquetas',
                    'package'               => 'Paquete',
                    'owner'                 => 'Propietario',
                    'source-location'       => 'Ubicación de Origen',
                    'destination-location'  => 'Ubicación de Destino',
                    'source-document'       => 'Documento de Origen',
                    'company'               => 'Empresa',
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
