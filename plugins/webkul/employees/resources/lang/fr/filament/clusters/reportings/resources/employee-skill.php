<?php

return [
    'title' => 'Compétences',

    'navigation' => [
        'title' => 'Compétences',
    ],

    'form' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Détails de la compétence',

                'fields' => [
                    'employee'       => 'Employé',
                    'skill'          => 'Compétence',
                    'skill-level'    => 'Niveau',
                    'skill-type'     => 'Type de compétence',
                ],
            ],
            'addition-information' => [
                'title' => 'Informations supplémentaires',

                'fields' => [
                    'created-by' => 'Créé par',
                    'updated-by' => 'Mis à jour par',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'employee'        => 'Employé',
            'skill'           => 'Compétence',
            'skill-level'     => 'Niveau',
            'skill-type'      => 'Type de compétence',
            'user'            => 'Utilisateur',
            'proficiency'     => 'Maîtrise',
            'created-by'      => 'Créé par',
            'created-at'      => 'Créé le',
        ],

        'filters' => [
            'employee'        => 'Employé',
            'skill'           => 'Compétence',
            'skill-level'     => 'Niveau',
            'skill-type'      => 'Type de compétence',
            'user'            => 'Utilisateur',
            'created-by'      => 'Créé par',
            'created-at'      => 'Créé le',
            'updated-at'      => 'Mis à jour le',
        ],

        'groups' => [
            'employee'   => 'Employé',
            'skill-type' => 'Type de compétence',
        ],
    ],

    'infolist' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Détails de la compétence',

                'entries' => [
                    'employee'        => 'Employé',
                    'skill'           => 'Compétence',
                    'skill-level'     => 'Niveau',
                    'skill-type'      => 'Type de compétence',
                ],
            ],

            'additional-information' => [
                'title' => 'Informations supplémentaires',

                'entries' => [
                    'created-by' => 'Créé par',
                    'updated-by' => 'Mis à jour par',
                ],
            ],
        ],
    ],
];
