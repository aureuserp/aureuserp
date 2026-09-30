<?php

return [
    'label' => 'Retour',

    'modal' => [
        'form' => [
            'columns' => [
                'product'                 => 'Produit',
                'quantity'                => 'Quantité',
                'uom'                     => 'UdM',
                'excess-quantity-tooltip' => 'La quantité à retourner est supérieure à la quantité restante pouvant être retournée.',
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
        'excess-quantity' => [
            'body' => 'La quantité à retourner pour :product ne peut pas être supérieure à la quantité restante pouvant être retournée (:quantity).',
        ],
    ],
];
