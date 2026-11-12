<?php

return [
    'locations' => [
        'pre-production'  => 'Pré-produção',
        'post-production' => 'Pós-produção',
    ],

    'operation-types' => [
        'pick-components'        => 'Separar componentes',
        'store-finished-product' => 'Armazenar produto acabado',
        'manufacturing'          => 'Fabricação',
    ],

    'routes' => [
        'manufacture-one-step'    => ':warehouse: Fabricar (1 etapa)',
        'manufacture-two-steps'   => ':warehouse: Separar componentes e depois fabricar (2 etapas)',
        'manufacture-three-steps' => ':warehouse: Separar componentes, fabricar e depois armazenar produtos (3 etapas)',
    ],

    'rules' => [
        'stock-pre-production'      => ':code: Estoque → Pré-produção',
        'pre-production-production' => ':code: Pré-produção → Produção',
        'post-production-stock'     => ':code: Pós-produção → Estoque',
    ],
];
