<?php

return [
    'columns' => [
        'asset-tag'          => 'Etiqueta del activo',
        'product'            => 'Producto',
        'product-type'       => 'Tipo de Producto',
        'initial-quantity'   => 'Cantidad inicial',
        'quantity'           => 'Cantidad',
        'model'              => 'Modelo',
        'category'           => 'Categoría',
        'serial-no'          => 'Número de serie',
        'status'             => 'Estado',
        'condition'          => 'Condición',
        'warehouse'          => 'Almacén',
        'current-employee'   => 'Empleado actual',
        'department'         => 'Departamento',
        'current-site'       => 'Ubicación actual',
        'site-type'          => 'Tipo de ubicación',
        'issued-at'          => 'Emitido el',
        'expected-return-at' => 'Fecha prevista de devolución',
        'cost'               => 'Coste',
        'purchase-date'      => 'Fecha de compra',
        'warranty-until'     => 'Garantía hasta',
        'custody-note'       => 'Nota de custodia',
        'note'               => 'Nota',
        'company'            => 'Compañía',
        'created-at'         => 'Creado el',
    ],

    'options' => [
        'section'    => 'Filtrar activos exportados (Opcional)',
        'products'   => 'Productos',
        'statuses'   => 'Estados',
        'conditions' => 'Condiciones',
        'categories' => 'Categorías',
        'warehouses' => 'Almacenes',
        'sites'      => 'Ubicaciones',
        'employees'  => 'Empleados',
    ],

    'notification' => [
        'completed' => 'La exportación del activo se ha completado y se exportaron :count fila(s).',
        'failed'    => 'No se pudieron exportar :count fila(s).',
    ],
];
