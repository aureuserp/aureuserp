<?php

return [
    'columns' => [
        'number'           => 'Número',
        'state'            => 'Estado',
        'customer'         => 'Cliente',
        'invoice-date'     => 'Fecha de factura',
        'due-date'         => 'Fecha de vencimiento',
        'tax-excluded'     => 'Impuestos excluidos',
        'tax'              => 'Impuesto',
        'total'            => 'Total',
        'amount-due'       => 'Importe adeudado',
        'payment-state'    => 'Estado de pago',
        'checked'          => 'Verificado',
        'accounting-date'  => 'Fecha contable',
        'source-document'  => 'Documento de origen',
        'reference'        => 'Referencia',
        'sales-person'     => 'Comercial',
        'invoice-currency' => 'Moneda de la factura',
    ],

    'values' => [
        'yes' => 'Sí',
        'no'  => 'No',
    ],

    'notification' => [
        'completed' => 'La exportación de la factura se ha completado y se exportaron :count fila(s).',
        'failed'    => 'No se pudieron exportar :count fila(s).',
    ],
];
