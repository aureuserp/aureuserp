<?php

return [
    'navigation' => [
        'title' => 'Movimientos',
    ],

    'label'        => 'Movimiento de Activo',
    'plural-label' => 'Movimientos de Activos',

    'table' => [
        'columns' => [
            'occurred-at'        => 'Fecha y Hora',
            'asset-tag'          => 'Etiqueta de Activo',
            'asset-name'         => 'Nombre de Activo',
            'type'               => 'Tipo de Movimiento',
            'quantity'           => 'Cantidad',
            'source-type'        => 'Origen',
            'from'               => 'Desde',
            'to'                 => 'Hacia',
            'condition'          => 'Condición',
            'performed-by'       => 'Realizado por',
            'expected-return-at' => 'Retorno Esperado',
            'note'               => 'Nota',
            'attachments'        => 'Adjuntos',
        ],
        'groups' => [
            'date'         => 'Fecha',
            'type'         => 'Tipo de Movimiento',
            'asset'        => 'Activo',
            'condition'    => 'Condición',
            'performed-by' => 'Realizado por',
        ],
        'filters' => [
            'type'         => 'Tipo de Movimiento',
            'product-type' => 'Tipo de Producto',
            'source-type'  => 'Origen',
            'condition'    => 'Condición',
            'category'     => 'Categoría',
            'product'      => 'Producto',
            'asset'        => 'Activo',
            'warehouse'    => 'Almacén',
            'employee'     => 'Empleado',
            'department'   => 'Departamento',
            'site'         => 'Sitio',
            'performed-by' => 'Realizado por',
            'date-from'    => 'Desde la Fecha',
            'date-until'   => 'Hasta la Fecha',
        ],
        'actions' => [
            'view' => 'Ver Detalles',
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'Exportar',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'tabs' => [
                'all'           => 'Todos',
                'assets'        => 'Activos',
                'consumables'   => 'Consumibles',
                'issues'        => 'Entregas',
                'returns'       => 'Devoluciones',
                'transfers'     => 'Transferencias',
                'scrapped-lost' => 'Desguazado / Perdido',
                'damaged'       => 'Dañado / Deficiente',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'details'     => 'Información del Movimiento',
            'attachments' => 'Adjuntos y Documentación',
        ],
    ],
];
