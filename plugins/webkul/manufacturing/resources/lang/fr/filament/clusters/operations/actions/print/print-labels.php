<?php

return [
    'label' => 'Imprimer les étiquettes',

    'form' => [
        'fields' => [
            'quantity'      => 'Quantité',
            'format'        => 'Format',
            'quantity-type' => 'Quantité à imprimer',

            'quantity-type-options' => [
                'operation' => 'Quantité de l\'opération',
                'custom'    => 'Quantité personnalisée',
            ],

            'format-options' => [
                'dymo'       => 'Dymo',
                '2x7_price'  => '2x7 avec prix',
                '4x7_price'  => '4x7 avec prix',
                '4x12'       => '4x12',
                '4x12_price' => '4x12 avec prix',
            ],
        ],
    ],
];
