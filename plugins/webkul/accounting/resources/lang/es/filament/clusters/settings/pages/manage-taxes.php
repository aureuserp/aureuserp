<?php

return [
    'title' => 'Gestionar impuestos',

    'form' => [
        'default-taxes' => [
            'label'       => 'Impuestos predeterminados',
            'helper-text' => 'El predeterminado se aplicará a los productos si no se selecciona ningún impuesto',
        ],

        'sales-tax' => [
            'label' => 'Impuesto sobre ventas',
        ],

        'purchase-tax' => [
            'label' => 'Impuesto sobre compras',
        ],

        'prices' => [
            'label' => 'Precios',
        ],

        'rounding-method' => [
            'label'       => 'Método de redondeo',
            'helper-text' => 'Método utilizado para redondear los importes de impuestos',

            'options' => [
                'round-per-line' => 'Redondear por línea',
                'round-globally' => 'Redondear globalmente',
            ],
        ],

        'fiscal-country' => [
            'label' => 'País fiscal',
        ],
    ],
];
