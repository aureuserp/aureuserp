<?php

return [
    'title' => 'Postes',

    'navigation' => [
        'title' => 'Postes',
        'group' => 'Recrutement',
    ],

    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Informations sur l\'emploi',

                'fields' => [
                    'job-position-title'         => 'Intitulé du poste',
                    'job-position-title-tooltip' => 'Saisissez l\'intitulé officiel du poste',
                    'department'                 => 'Département',
                    'department-modal-title'     => 'Créer un département',
                    'company-modal-title'        => 'Créer une société',
                    'job-location'               => 'Lieu de travail',
                    'industry'                   => 'Secteur d\'activité',
                    'company'                    => 'Société',
                    'employment-type'            => 'Type d\'emploi',
                    'recruiter'                  => 'Recruteur',
                    'interviewer'                => 'Intervieweur',
                ],
            ],

            'job-description' => [
                'title' => 'Description du poste',

                'fields' => [
                    'job-description'  => 'Description du poste',
                    'job-requirements' => 'Exigences du poste',
                ],
            ],

            'workforce-planning' => [
                'title' => 'Planification des effectifs',

                'fields' => [
                    'recruitment-target'         => 'Objectif de recrutement',
                    'date-from'                  => 'Date de début',
                    'date-to'                    => 'Date de fin',
                    'expected-skills'            => 'Compétences attendues',
                    'employment-type'            => 'Type d\'emploi',
                    'status'                     => 'Statut',
                ],
            ],

            'position-status' => [
                'title' => 'Statut du poste',

                'fields' => [
                    'status' => 'Statut',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Poste',
            'department'         => 'Département',
            'job-position'       => 'Poste',
            'company'            => 'Société',
            'expected-employees' => 'Employés attendus',
            'current-employees'  => 'Employés actuels',
            'status'             => 'Statut',
            'created-by'         => 'Créé par',
            'created-at'         => 'Créé le',
            'updated-at'         => 'Mis à jour le',
        ],

        'filters' => [
            'department'      => 'Département',
            'employment-type' => 'Type d\'emploi',
            'job-position'    => 'Poste',
            'company'         => 'Société',
            'status'          => 'Statut',
            'created-by'      => 'Créé par',
            'updated-at'      => 'Mis à jour le',
            'created-at'      => 'Créé le',
        ],

        'groups' => [
            'job-position'    => 'Poste',
            'company'         => 'Société',
            'department'      => 'Département',
            'employment-type' => 'Type d\'emploi',
            'created-by'      => 'Créé par',
            'created-at'      => 'Créé le',
            'updated-at'      => 'Mis à jour le',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Poste restauré',
                    'body'  => 'Le poste a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Poste supprimé',
                    'body'  => 'Le poste a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Postes restaurés',
                    'body'  => 'Les postes ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Postes supprimés',
                    'body'  => 'Les postes ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Postes supprimés définitivement',
                    'body'  => 'Les postes ont été supprimés définitivement avec succès.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Postes',
                    'body'  => 'Les postes ont été créés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Informations sur l\'emploi',

                'entries' => [
                    'job-position-title' => 'Intitulé du poste',
                    'department'         => 'Département',
                    'company'            => 'Société',
                    'employment-type'    => 'Type d\'emploi',
                    'job-location'       => 'Lieu de travail',
                    'industry'           => 'Secteur d\'activité',
                ],
            ],
            'job-description' => [
                'title' => 'Description du poste',

                'entries' => [
                    'job-description'  => 'Description du poste',
                    'job-requirements' => 'Exigences du poste',
                ],
            ],
            'work-planning' => [
                'title' => 'Planification des effectifs',

                'entries' => [
                    'expected-employees' => 'Employés attendus',
                    'current-employees'  => 'Employés actuels',
                    'date-from'          => 'Date de début',
                    'date-to'            => 'Date de fin',
                    'recruitment-target' => 'Objectif de recrutement',
                ],
            ],
            'position-status' => [
                'title' => 'Statut du poste',

                'entries' => [
                    'status' => 'Statut',
                ],
            ],
        ],
    ],
];
