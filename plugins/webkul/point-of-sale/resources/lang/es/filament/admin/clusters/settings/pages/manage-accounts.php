<?php

return [
    'title' => 'Contabilidad',

    'form' => [
        'fields' => [
            'receivable-account'      => 'Cuenta a cobrar predeterminada',
            'stock-output-account'    => 'Cuenta de salida de stock',
            'balancing-account'       => 'Cuenta de ajuste predeterminada',
            'enable-cogs'             => 'Contabilizar el coste de las ventas',
            'enable-cogs-helper-text' => 'Contabiliza un asiento de coste a partir de la instantánea del coste del producto. Todavía no hay capa de valoración, por lo que la cuenta de salida de stock funciona como contrapartida del coste de ventas.',
            'allow-balancing-line'    => 'Permitir línea de ajuste',
        ],
    ],
];
