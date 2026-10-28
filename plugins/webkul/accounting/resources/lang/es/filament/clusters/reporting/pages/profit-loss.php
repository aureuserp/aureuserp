<?php

return [
    'period' => 'Del :from al :to',

    'filters' => [
        'ranges' => [
            'today'        => 'Hoy',
            'yesterday'    => 'Ayer',
            'this-month'   => 'Este mes',
            'last-month'   => 'Mes pasado',
            'this-quarter' => 'Este trimestre',
            'last-quarter' => 'Trimestre pasado',
            'this-year'    => 'Este año',
            'last-year'    => 'Año pasado',
        ],
    ],

    'table' => [
        'columns' => [
            'amount' => 'Importe',
        ],
    ],

    'totals' => [
        'net-profit' => 'Beneficio neto',
        'net-loss'   => 'Pérdida neta',
    ],

    'footer' => [
        'generated-on' => 'Generado el :date',
    ],
];
