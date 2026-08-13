<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'name'  => 'Nom',
                    'code'  => "Code d'identification bancaire",
                    'email' => 'E-mail',
                    'phone' => 'Téléphone',
                ],
            ],

            'address' => [
                'title' => 'Adresse',

                'fields' => [
                    'address' => 'Adresse',
                    'city'    => 'Ville',
                    'street1' => 'Rue 1',
                    'street2' => 'Rue 2',
                    'state'   => 'État',
                    'zip'     => 'Code postal',
                    'country' => 'Pays',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Nom',
            'code'           => "Code d'identification bancaire",
            'country'        => 'Pays',
            'created-at'     => 'Créé le',
            'updated-at'     => 'Mis à jour le',
            'deleted-at'     => 'Supprimé le',
        ],

        'groups' => [
            'country'               => 'Pays',
            'created-at'            => 'Créé le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Banque mise à jour',
                    'body'  => 'La banque a été mise à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Banque restaurée',
                    'body'  => 'La banque a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Banque supprimée',
                    'body'  => 'La banque a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Banque définitivement supprimée',
                    'body'  => 'La banque a été définitivement supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Banques restaurées',
                    'body'  => 'Les banques ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Banques supprimées',
                    'body'  => 'Les banques ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Banques définitivement supprimées',
                    'body'  => 'Les banques ont été définitivement supprimées avec succès.',
                ],
            ],
        ],
    ],
];
