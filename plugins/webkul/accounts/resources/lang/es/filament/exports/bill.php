<?php

return [
    'columns' => [
        'number'          => 'Número',
        'state'           => 'Estado',
        'customer'        => 'Cliente',
        'bill-date'       => 'Fecha de factura',
        'due-date'        => 'Fecha de vencimiento',
        'tax-excluded'    => 'Impuestos excluidos',
        'tax'             => 'Impuesto',
        'total'           => 'Total',
        'amount-due'      => 'Importe adeudado',
        'payment-state'   => 'Estado de pago',
        'checked'         => 'Verificado',
        'accounting-date' => 'Fecha contable',
        'source-document' => 'Documento de origen',
        'reference'       => 'Referencia',
        'sales-person'    => 'Comercial',
        'bill-currency'   => 'Moneda de la factura',
    ],

    'values' => [
        'yes' => 'Sí',
        'no'  => 'No',
    ],

    'notification' => [
        'completed' => 'La exportación de la factura de proveedor se ha completado y se exportaron :count fila(s).',
        'failed'    => 'No se pudieron exportar :count fila(s).',
    ],
];
