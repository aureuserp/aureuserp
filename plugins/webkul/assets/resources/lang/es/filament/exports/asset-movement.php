<?php

return [
    'columns' => [
        'occurred-at'        => 'Fecha y hora',
        'asset-tag'          => 'Etiqueta del activo',
        'product'            => 'Producto',
        'model'              => 'Modelo',
        'category'           => 'Categoría',
        'serial-no'          => 'Número de serie',
        'type'               => 'Tipo de movimiento',
        'quantity'           => 'Cantidad',
        'source-type'        => 'Tipo de origen',
        'from'               => 'Desde',
        'to'                 => 'Hacia',
        'condition'          => 'Condición',
        'expected-return-at' => 'Fecha prevista de devolución',
        'performed-by'       => 'Realizado por',
        'note'               => 'Nota',
        'company'            => 'Compañía',
    ],

    'options' => [
        'section'    => 'Filtrar movimientos exportados (Opcional)',
        'date-from'  => 'Desde la fecha',
        'date-until' => 'Hasta la fecha',
        'types'      => 'Tipos de movimiento',
        'sites'      => 'Ubicaciones',
        'employees'  => 'Empleados',
    ],

    'notification' => [
        'completed' => 'La exportación de movimientos de activos se ha completado y se exportaron :count fila(s).',
        'failed'    => 'No se pudieron exportar :count fila(s).',
    ],
];
