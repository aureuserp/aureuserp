<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'company'            => 'Société',
                'country'            => 'Pays',
                'name'               => 'Nom',
                'preceding-subtotal' => 'Sous-total précédent',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company'            => 'Société',
            'country'            => 'Pays',
            'created-by'         => 'Créé par',
            'name'               => 'Nom',
            'preceding-subtotal' => 'Sous-total précédent',
            'created-at'         => 'Créé le',
            'updated-at'         => 'Mis à jour le',
        ],

        'groups' => [
            'name'       => 'Nom',
            'company'    => 'Société',
            'country'    => 'Pays',
            'created-by' => 'Créé par',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Groupe de taxes supprimé',
                        'body'  => 'Le groupe de taxes a été supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'Le groupe de taxes n\'a pas pu être supprimé',
                        'body'  => 'Le groupe de taxes ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Groupes de taxes supprimés',
                        'body'  => 'Les groupes de taxes ont été supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les groupes de taxes n\'ont pas pu être supprimés',
                        'body'  => 'Les groupes de taxes ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'company'            => 'Société',
                'country'            => 'Pays',
                'name'               => 'Nom',
                'preceding-subtotal' => 'Sous-total précédent',
            ],
        ],
    ],
];
