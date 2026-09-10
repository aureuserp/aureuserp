<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimer les étiquettes',

            'form' => [
                'fields' => [
                    'quantity' => 'Nombre d\'étiquettes',
                    'format'   => 'Format',

                    'format-options' => [
                        'dymo'       => 'Dymo',
                        '2x7_price'  => '2x7 avec prix',
                        '4x7_price'  => '4x7 avec prix',
                        '4x12'       => '4x12',
                        '4x12_price' => '4x12 avec prix',
                    ],
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Produit supprimé',
                'body'  => 'Le produit a été supprimé avec succès.',
            ],
        ],
    ],
];
