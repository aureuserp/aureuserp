<?php

return [
    'label' => 'Retour',

    'modal' => [
        'form' => [
            'columns' => [
                'product'                 => 'Produit',
                'quantity'                => 'Quantité',
                'uom'                     => 'UdM',
                'excess-quantity-tooltip' => 'La quantité à retourner est supérieure à la quantité traitée dans l\'opération d\'origine.',
            ],
        ],
    ],

    'notification' => [
        'no-products' => [
            'body' => 'Aucun produit à retourner (seules les lignes à l\'état Terminé et non encore entièrement retournées peuvent être retournées).',
        ],
        'no-quantities' => [
            'body' => 'Veuillez indiquer au moins une quantité non nulle.',
        ],
    ],
];
