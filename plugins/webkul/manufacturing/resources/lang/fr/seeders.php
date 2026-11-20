<?php

return [
    'locations' => [
        'pre-production'  => 'Pré-production',
        'post-production' => 'Post-production',
    ],

    'operation-types' => [
        'pick-components'        => 'Prélever les composants',
        'store-finished-product' => 'Stocker le produit fini',
        'manufacturing'          => 'Fabrication',
    ],

    'routes' => [
        'manufacture-one-step'    => ':warehouse : Fabriquer (1 étape)',
        'manufacture-two-steps'   => ':warehouse : Prélever les composants puis fabriquer (2 étapes)',
        'manufacture-three-steps' => ':warehouse : Prélever les composants, fabriquer puis stocker les produits (3 étapes)',
    ],

    'rules' => [
        'stock-pre-production'      => ':code : Stock → Pré-production',
        'pre-production-production' => ':code : Pré-production → Production',
        'post-production-stock'     => ':code : Post-production → Stock',
    ],
];
