<?php

return [
    'columns' => [
        'date'            => 'Fecha',
        'name'            => 'Nombre',
        'journal'         => 'Diario',
        'payment-method'  => 'Método de pago',
        'partner'         => 'Contacto',
        'amount-currency' => 'Moneda del importe',
        'amount'          => 'Importe',
        'state'           => 'Estado',
        'company'         => 'Empresa',
    ],

    'notification' => [
        'completed' => 'La exportación del pago se ha completado y se exportaron :count fila(s).',
        'failed'    => 'No se pudieron exportar :count fila(s).',
    ],
];
