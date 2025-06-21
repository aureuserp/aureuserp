<?php

return [
    'navigation' => [
        'title' => 'Productos',
        'group' => 'Inventario',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'receive-from'          => 'Recibir De',
                    'contact'               => 'Contacto',
                    'delivery-address'      => 'Dirección de Entrega',
                    'operation-type'        => 'Tipo de Operación',
                    'source-location'       => 'Ubicación de Origen',
                    'destination-location'  => 'Ubicación de Destino',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title' => 'Operaciones',

                'fields' => [
                    'product'           => 'Producto',
                    'final-location'    => 'Ubicación Final',
                    'description'       => 'Descripción',
                    'scheduled-at'      => 'Programado Para',
                    'deadline'          => 'Fecha Límite',
                    'packaging'         => 'Embalaje',
                    'demand'            => 'Demanda',
                    'quantity'          => 'Cantidad',
                    'unit'              => 'Unidad',
                    'picked'            => 'Recogido',

                    'lines' => [
                        'modal-heading' => 'Gestionar Movimientos de Stock',
                        'add-line'      => 'Agregar Línea',

                        'fields' => [
                            'lot'       => 'Lote/Número de Serie',
                            'pick-from' => 'Recoger De',
                            'location'  => 'Almacenar En',
                            'package'   => 'Paquete de Destino',
                            'quantity'  => 'Cantidad',
                            'uom'       => 'Unidad de Medida',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Adicional',

                'fields' => [
                    'responsible'                       => 'Responsable',
                    'shipping-policy'                   => 'Política de Envío',
                    'shipping-policy-hint-tooltip'      => 'Define si los productos deben entregarse parcialmente o todos a la vez.',
                    'scheduled-at'                      => 'Programado Para',
                    'scheduled-at-hint-tooltip'         => 'La hora programada para procesar la primera parte del envío. Establecer manualmente un valor aquí lo aplicará como la fecha esperada para todos los movimientos de stock.',
                    'source-document'                   => 'Documento de Origen',
                    'source-document-hint-tooltip'      => 'Referencia del documento',
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
            'reference'         => 'Referencia',
            'from'              => 'De',
            'to'                => 'A',
            'contact'           => 'Contacto',
            'responsible'       => 'Responsable',
            'scheduled-at'      => 'Programado Para',
            'deadline'          => 'Fecha Límite',
            'closed-at'         => 'Cerrado El',
            'source-document'   => 'Documento de Origen',
            'operation-type'    => 'Tipo de Operación',
            'company'           => 'Empresa',
            'state'             => 'Estado',
            'deleted-at'        => 'Eliminado El',
            'created-at'        => 'Creado El',
            'updated-at'        => 'Actualizado El',
        ],

        'groups' => [
            'state'             => 'Estado',
            'source-document'   => 'Documento de Origen',
            'operation-type'    => 'Tipo de Operación',
            'schedule-at'       => 'Programado Para',
            'created-at'        => 'Creado El',
        ],

        'filters' => [
            'name'                  => 'Nombre',
            'state'                 => 'Estado',
            'partner'               => 'Socio',
            'responsible'           => 'Responsable',
            'owner'                 => 'Propietario',
            'source-location'       => 'Ubicación de Origen',
            'destination-location'  => 'Ubicación de Destino',
            'deadline'              => 'Fecha Límite',
            'scheduled-at'          => 'Programado Para',
            'closed-at'             => 'Cerrado El',
            'created-at'            => 'Creado El',
            'updated-at'            => 'Actualizado El',
            'company'               => 'Empresa',
            'creator'               => 'Creador',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información General',
                'entries' => [
                    'contact'               => 'Contacto',
                    'operation-type'        => 'Tipo de Operación',
                    'source-location'       => 'Ubicación de Origen',
                    'destination-location'  => 'Ubicación de Destino',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title'   => 'Operaciones',
                'entries' => [
                    'product'           => 'Producto',
                    'final-location'    => 'Ubicación Final',
                    'description'       => 'Descripción',
                    'scheduled-at'      => 'Programado Para',
                    'deadline'          => 'Fecha Límite',
                    'packaging'         => 'Embalaje',
                    'demand'            => 'Demanda',
                    'quantity'          => 'Cantidad',
                    'unit'              => 'Unidad',
                    'picked'            => 'Recogido',
                ],
            ],
            'additional' => [
                'title'   => 'Información Adicional',
                'entries' => [
                    'responsible'       => 'Responsable',
                    'shipping-policy'   => 'Política de Envío',
                    'scheduled-at'      => 'Programado Para',
                    'source-document'   => 'Documento de Origen',
                ],
            ],
            'note' => [
                'title' => 'Nota',
            ],
        ],
    ],

    'tabs' => [
        'todo'     => 'Por Hacer',
        'my'       => 'Mis Transferencias',
        'starred'  => 'Destacado',
        'draft'    => 'Borrador',
        'waiting'  => 'Esperando',
        'ready'    => 'Listo',
        'done'     => 'Hecho',
        'canceled' => 'Cancelado',
    ],
];
