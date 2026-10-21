<?php

return [
    'navigation' => [
        'title' => 'Catégories de produits PdV',
        'group' => 'Point de vente',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'name'    => 'Nom',
                    'parent'  => 'Catégorie parente',
                    'company' => 'Société',
                    'color'   => 'Couleur',
                    'image'   => 'Image',
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
                    'parent-name'  => 'Catégorie parente',
                    'color'        => 'Couleur',
                    'company-name' => 'Société',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'    => 'Nom',
            'parent'  => 'Catégorie parente',
            'color'   => 'Couleur',
            'company' => 'Société',
        ],

        'groups' => [
            'parent' => 'Catégorie parente',
        ],
    ],
];
