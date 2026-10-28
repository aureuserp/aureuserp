<?php

return [
    'heading' => ':report - Al :date',
    'as-of'   => 'Al :date',

    'table' => [
        'columns' => [
            'at-date'      => 'No vencido',
            'period-range' => ':from-:to',
            'older'        => 'Más antiguo',
            'older-than'   => ':days+',
        ],
    ],

    'totals' => [
        'total' => 'Total antigüedad de cuentas por cobrar',
    ],

    'messages' => [
        'unposted-entries' => 'Hay asientos contables sin contabilizar anteriores o incluidos en este período.',
        'empty'            => 'No se encontraron cuentas por cobrar para los criterios seleccionados.',
    ],

    'footer' => [
        'generated-on' => 'Generado el :date',
    ],
];
