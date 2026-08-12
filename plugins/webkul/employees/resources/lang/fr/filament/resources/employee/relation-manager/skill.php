<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'skill-type'  => 'Type de compétence',
                'skill'       => 'Compétence',
                'skill-level' => 'Niveau de compétence',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'skill-type'    => 'Type de compétence',
            'skill'         => 'Compétence',
            'skill-level'   => 'Niveau de compétence',
            'level-percent' => 'Pourcentage du niveau',
            'created-by'    => 'Créé par',
            'user'          => 'Utilisateur',
            'created-at'    => 'Créé le',
        ],

        'groups' => [
            'skill-type' => 'Type de compétence',
        ],

        'header-actions' => [
            'add-skill' => 'Ajouter une compétence',
        ],

        'filters' => [
            'activity-type'   => 'Type d\'activité',
            'activity-status' => 'Statut de l\'activité',
            'has-delay'       => 'A un délai',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Compétence mise à jour',
                    'body'  => 'La compétence a été mise à jour avec succès.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Compétence créée',
                    'body'  => 'La compétence a été créée avec succès.',
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
        ],
    ],

    'infolist' => [
        'entries' => [
            'skill-type'    => 'Type de compétence',
            'skill'         => 'Compétence',
            'skill-level'   => 'Niveau de compétence',
            'level-percent' => 'Pourcentage du niveau',
        ],
    ],
];
