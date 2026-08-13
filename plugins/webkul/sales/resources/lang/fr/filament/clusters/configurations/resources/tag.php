<?php

return [
    'title' => 'Étiquette',

    'navigation' => [
        'title' => 'Étiquette',
        'group' => 'Commandes clients',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Nom',
            'color' => 'Couleur',
        ],
    ],

    'table' => [
        'columns' => [
            'created-by' => 'Créé par',
            'name'       => 'Nom',
            'color'      => 'Couleur',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Étiquette produit mise à jour',
                    'body'  => 'L\'étiquette produit a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Étiquette produit supprimée',
                    'body'  => 'L\'étiquette produit a été supprimée avec succès.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Étiquette produit supprimée',
                    'body'  => 'L\'étiquette produit a été supprimée avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'Nom',
            'color' => 'Couleur',
        ],
    ],
];
