<?php

return [
    'columns' => [
        'invoice-date' => 'Fecha de factura',
        'date'         => 'Fecha',
        'number'       => 'Número',
        'partner'      => 'Contacto',
        'reference'    => 'Referencia',
        'journal'      => 'Diario',
        'company'      => 'Empresa',
        'total'        => 'Total',
        'state'        => 'Estado',
        'checked'      => 'Verificado',
    ],

    'values' => [
        'yes' => 'Sí',
        'no'  => 'No',
    ],

    'notification' => [
        'completed' => 'La exportación de su asiento contable se ha completado y se exportaron :count fila(s).',
        'failed'    => 'No se pudieron exportar :count fila(s).',
    ],
];
