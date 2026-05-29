<?php

return [
    'navigation' => [
        'title' => 'Productos',
        'group' => 'Inventario',
    ],

    'global-search' => [
        'partner' => 'Contacto',
        'origin'  => 'Origen',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'receive-from'         => 'Recibir de',
                    'contact'              => 'Contacto',
                    'delivery-address'     => 'Dirección de entrega',
                    'operation-type'       => 'Tipo de operación',
                    'source-location'      => 'Ubicación de origen',
                    'destination-location' => 'Ubicación de destino',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title' => 'Operaciones',

                'columns' => [
                    'product'                    => 'Producto',
                    'final-location'             => 'Ubicación final',
                    'description'                => 'Descripción',
                    'scheduled-at'               => 'Programado el',
                    'deadline'                   => 'Fecha límite',
                    'packaging'                  => 'Empaque',
                    'demand'                     => 'Demanda',
                    'quantity'                   => 'Cantidad',
                    'insufficient-stock-tooltip' => 'Cantidad disponible insuficiente',
                    'unit'                       => 'Unidad',
                    'picked'                     => 'Recogido',
                ],

                'fields' => [
                    'product'        => 'Producto',
                    'final-location' => 'Ubicación final',
                    'description'    => 'Descripción',
                    'scheduled-at'   => 'Programado el',
                    'deadline'       => 'Fecha límite',
                    'packaging'      => 'Empaque',
                    'demand'         => 'Demanda',
                    'quantity'       => 'Cantidad',
                    'unit'           => 'Unidad',
                    'picked'         => 'Recogido',

                    'lines' => [
                        'modal-heading' => 'Gestionar movimientos de existencias',
                        'add-line'      => 'Agregar línea',

                        'fields' => [
                            'lot'       => 'Lote/Número de serie',
                            'pick-from' => 'Tomar de',
                            'location'  => 'Almacenar en',
                            'package'   => 'Paquete de destino',
                            'quantity'  => 'Cantidad',
                            'uom'       => 'Unidad de medida',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Adicional',

                'fields' => [
                    'responsible'                  => 'Responsable',
                    'shipping-policy'              => 'Política de envío',
                    'shipping-policy-hint-tooltip' => 'Define si los bienes deben entregarse parcialmente o todos a la vez.',
                    'scheduled-at'                 => 'Programado el',
                    'scheduled-at-hint-tooltip'    => 'La hora programada para procesar la primera parte del envío. Establecer un valor manualmente aquí lo aplicará como fecha esperada para todos los movimientos de existencias.',
                    'source-document'              => 'Documento de origen',
                    'source-document-hint-tooltip' => 'Referencia del documento',
                ],
            ],

            'note' => [
                'title' => 'Nota',

                'fields' => [

                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'        => 'Favorito',
            'reference'       => 'Referencia',
            'from'            => 'De',
            'to'              => 'A',
            'contact'         => 'Contacto',
            'responsible'     => 'Responsable',
            'scheduled-at'    => 'Programado el',
            'deadline'        => 'Fecha límite',
            'closed-at'       => 'Cerrado el',
            'source-document' => 'Documento de origen',
            'operation-type'  => 'Tipo de operación',
            'company'         => 'Empresa',
            'state'           => 'Estado',
            'deleted-at'      => 'Eliminado el',
            'created-at'      => 'Creado el',
            'updated-at'      => 'Actualizado el',
        ],

        'groups' => [
            'state'           => 'Estado',
            'source-document' => 'Documento de origen',
            'operation-type'  => 'Tipo de operación',
            'scheduled-at'    => 'Programar el',
            'created-at'      => 'Creado el',
        ],

        'filters' => [
            'name'                 => 'Nombre',
            'state'                => 'Estado',
            'partner'              => 'Contacto',
            'responsible'          => 'Responsable',
            'owner'                => 'Propietario',
            'source-location'      => 'Ubicación de origen',
            'destination-location' => 'Ubicación de destino',
            'deadline'             => 'Fecha límite',
            'scheduled-at'         => 'Programado el',
            'closed-at'            => 'Cerrado el',
            'created-at'           => 'Creado el',
            'updated-at'           => 'Actualizado el',
            'company'              => 'Empresa',
            'creator'              => 'Creador',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información general',
                'entries' => [
                    'contact'              => 'Contacto',
                    'operation-type'       => 'Tipo de operación',
                    'source-location'      => 'Ubicación de origen',
                    'destination-location' => 'Ubicación de destino',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title'   => 'Operaciones',
                'entries' => [
                    'product'        => 'Producto',
                    'final-location' => 'Ubicación final',
                    'description'    => 'Descripción',
                    'scheduled-at'   => 'Programado el',
                    'deadline'       => 'Fecha límite',
                    'packaging'      => 'Empaque',
                    'demand'         => 'Demanda',
                    'quantity'       => 'Cantidad',
                    'unit'           => 'Unidad',
                    'picked'         => 'Recogido',
                ],
            ],
            'additional' => [
                'title'   => 'Información adicional',
                'entries' => [
                    'responsible'     => 'Responsable',
                    'shipping-policy' => 'Política de envío',
                    'scheduled-at'    => 'Programado el',
                    'source-document' => 'Documento de origen',
                ],
            ],
            'note' => [
                'title' => 'Nota',
            ],
        ],
    ],

    'tabs' => [
        'todo'     => 'Por hacer',
        'my'       => 'Mis transferencias',
        'starred'  => 'Destacados',
        'draft'    => 'Borrador',
        'waiting'  => 'En espera',
        'ready'    => 'Listo',
        'done'     => 'Realizado',
        'canceled' => 'Cancelado',
    ],

    'notifications' => [
        'uom-precision-warning' => [
            'title' => 'Advertencia de precisión de unidad de medida',
            'body'  => 'Está usando una unidad de medida más pequeña que la utilizada para almacenar este producto. Esto puede generar problemas de redondeo en las cantidades reservadas. Considere usar la unidad de medida más pequeña para la valoración de existencias o reducir la precisión de redondeo de su unidad base.',
        ],
    ],
];
