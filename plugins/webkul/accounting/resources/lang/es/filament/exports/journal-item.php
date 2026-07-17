<?php

return [
    'columns' => [
        'number'           => 'Número',
        'date'             => 'Fecha',
        'account'          => 'Cuenta',
        'partner'          => 'Contacto',
        'label'            => 'Etiqueta',
        'reference'        => 'Referencia',
        'journal'          => 'Diario',
        'debit'            => 'Débito',
        'credit'           => 'Crédito',
        'balance'          => 'Saldo',
        'currency'         => 'Moneda',
        'company'          => 'Empresa',
        'status'           => 'Estado',
        'amount-currency'  => 'Importe en moneda',
        'amount-residual'  => 'Importe residual',
        'reconciled'       => 'Conciliado',
        'due-date'         => 'Fecha de vencimiento',
    ],

    'values' => [
        'yes' => 'Sí',
        'no'  => 'No',
    ],

    'notification' => [
        'completed' => 'La exportación de su apunte contable se ha completado y se exportaron :count fila(s).',
        'failed'    => 'No se pudieron exportar :count fila(s).',
    ],
];
