<?php

return [
    'form' => [
        'fields' => [
            'color'         => 'Couleur',
            'country'       => 'Pays',
            'applicability' => 'Applicabilité',
            'name'          => 'Nom',
            'status'        => 'Statut',
            'tax-negate'    => 'Taxe négative',
        ],
    ],

    'table' => [
        'columns' => [
            'color'         => 'Couleur',
            'country'       => 'Pays',
            'created-by'    => 'Créé par',
            'applicability' => 'Applicabilité',
            'name'          => 'Nom',
            'status'        => 'Statut',
            'tax-negate'    => 'Taxe négative',
            'created-at'    => 'Créé le',
            'updated-at'    => 'Mis à jour le',
            'deleted-at'    => 'Supprimé le',
        ],

        'filters' => [
            'bank'           => 'Banque',
            'account-holder' => 'Titulaire du compte',
            'creator'        => 'Créateur',
            'can-send-money' => 'Peut envoyer de l\'argent',
        ],

        'groups' => [
            'country'       => 'Pays',
            'created-by'    => 'Créé par',
            'applicability' => 'Applicabilité',
            'name'          => 'Nom',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Étiquette de compte mise à jour',
                    'body'  => 'L\'étiquette de compte a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Étiquette de compte supprimée',
                    'body'  => 'L\'étiquette de compte a été supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Étiquettes de compte supprimées',
                    'body'  => 'Les étiquettes de compte ont été supprimées avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'color'         => 'Couleur',
            'country'       => 'Pays',
            'applicability' => 'Applicabilité',
            'name'          => 'Nom',
            'status'        => 'Statut',
            'tax-negate'    => 'Taxe négative',
        ],
    ],
];
