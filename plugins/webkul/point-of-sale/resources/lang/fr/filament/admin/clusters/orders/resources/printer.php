<?php

return [
    'navigation' => [
        'title' => 'Imprimantes de préparation',
        'group' => 'Point de vente',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'name'         => 'Nom',
                    'printer-type' => 'Type d\'imprimante',
                    'proxy-ip'     => 'IP du proxy',
                    'company'      => 'Société',
                    'categories'   => 'Catégories imprimées',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Général',

                'entries' => [
                    'name'         => 'Nom',
                    'printer-type' => 'Type d\'imprimante',
                    'proxy-ip'     => 'Adresse IP',
                    'company-name' => 'Société',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nom',
            'printer-type' => 'Type d\'imprimante',
            'proxy-ip'     => 'IP du proxy',
            'categories'   => 'Catégories',
            'company'      => 'Société',
        ],

        'filters' => [
            'printer-type' => 'Type d\'imprimante',
        ],
    ],
];
