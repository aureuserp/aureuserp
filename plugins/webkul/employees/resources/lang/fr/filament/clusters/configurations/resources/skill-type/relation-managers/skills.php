<?php

return [
    'form' => [
        'name' => 'Nom',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'groups' => [
            'created-at' => 'Créé le',
        ],

        'filters' => [
            'deleted-records' => 'Enregistrements supprimés',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Compétence mise à jour',
                    'body'  => 'La compétence a été mise à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Compétence restaurée',
                    'body'  => 'La compétence a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Compétence supprimée',
                    'body'  => 'La compétence a été supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Compétences supprimées',
                    'body'  => 'Les compétences ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Compétences supprimées définitivement',
                    'body'  => 'Les compétences ont été supprimées définitivement avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Compétences restaurées définitivement',
                    'body'  => 'Les compétences ont été restaurées définitivement avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nom',
        ],
    ],
];
