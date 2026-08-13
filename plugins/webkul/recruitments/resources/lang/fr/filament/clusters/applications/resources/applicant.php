<?php

return [
    'title' => 'Candidat',

    'navigation' => [
        'title' => 'Candidats',
    ],

    'global-search' => [
        'department' => 'Département',
        'work-email' => 'E-mail professionnel',
        'work-phone' => 'Téléphone professionnel',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informations générales',

                'fields' => [
                    'evaluation-good'           => 'Évaluation : Bien',
                    'evaluation-very-good'      => 'Évaluation : Très bien',
                    'evaluation-very-excellent' => 'Évaluation : Excellent',
                    'hired'                     => 'Embauché',
                    'candidate-name'            => 'Nom du candidat',
                    'email'                     => 'E-mails',
                    'phone'                     => 'Téléphone',
                    'linkedin-profile'          => 'Profil Linkedin',
                    'recruiter'                 => 'Recruteur',
                    'interviewer'               => 'Intervieweur',
                    'tags'                      => 'Étiquettes',
                    'notes'                     => 'Notes',
                    'hired-date'                => 'Date d\'embauche',
                    'job-position'              => 'Postes',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Formation et disponibilité',

                'fields' => [
                    'degree'            => 'Diplôme',
                    'availability-date' => 'Date de disponibilité',
                ],
            ],

            'department' => [
                'title' => 'Département',
            ],

            'salary' => [
                'title' => 'Salaire attendu et proposé',

                'fields' => [
                    'expected-salary'       => 'Salaire attendu',
                    'salary-proposed-extra' => 'Autre avantage',
                    'proposed-salary'       => 'Salaire proposé',
                    'salary-expected-extra' => 'Autre avantage',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Source et support',

                'fields' => [
                    'source' => 'Source',
                    'medium' => 'Support',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'partner-name'       => 'Nom du partenaire',
            'applied-on'         => 'Candidature déposée le',
            'job-position'       => 'Poste',
            'stage'              => 'Étape',
            'candidate-name'     => 'Nom du candidat',
            'evaluation'         => 'Évaluation',
            'application-status' => 'Statut de la candidature',
            'tags'               => 'Étiquettes',
            'refuse-reason'      => 'Motif de refus',
            'email'              => 'E-mail',
            'recruiter'          => 'Recruteur',
            'interviewer'        => 'Intervieweur',
            'candidate-phone'    => 'Téléphone',
            'medium'             => 'Support',
            'source'             => 'Source',
            'salary-expected'    => 'Salaire attendu',
            'availability-date'  => 'Date de disponibilité',
        ],

        'filters' => [
            'source'                  => 'Source',
            'medium'                  => 'Support',
            'candidate'               => 'Candidat',
            'priority'                => 'Priorité',
            'salary-proposed-extra'   => 'Avantage proposé',
            'salary-expected-extra'   => 'Avantage attendu',
            'applicant-notes'         => 'Notes du candidat',
            'create-date'             => 'Candidature déposée le',
            'date-closed'             => 'Date d\'embauche',
            'date-last-stage-updated' => 'Dernière étape mise à jour',
            'stage'                   => 'Étape',
            'job-position'            => 'Poste',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidat supprimé',
                    'body'  => 'Le candidat a été supprimé avec succès.',
                ],
            ],
        ],

        'groups' => [
            'stage'          => 'Étape',
            'job-position'   => 'Poste',
            'candidate-name' => 'Nom du candidat',
            'responsible'    => 'Responsable',
            'creation-date'  => 'Date de création',
            'hired-date'     => 'Date d\'embauche',
            'last-stage'     => 'Dernière étape',
            'refuse-reason'  => 'Motif de refus',
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Employés supprimés',
                    'body'  => 'Les employés ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Employés supprimés',
                    'body'  => 'Les employés ont été supprimés avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Employés restaurés',
                    'body'  => 'Les employés ont été restaurés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informations générales',

                'entries' => [
                    'evaluation-good'           => 'Évaluation : Bien',
                    'evaluation-very-good'      => 'Évaluation : Très bien',
                    'evaluation-very-excellent' => 'Évaluation : Excellent',
                    'hired'                     => 'Embauché',
                    'candidate-name'            => 'Nom du candidat',
                    'email'                     => 'E-mails',
                    'phone'                     => 'Téléphone',
                    'linkedin-profile'          => 'Profil Linkedin',
                    'recruiter'                 => 'Recruteur',
                    'interviewer'               => 'Intervieweur',
                    'tags'                      => 'Étiquettes',
                    'notes'                     => 'Notes',
                    'job-position'              => 'Postes',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Formation et disponibilité',

                'entries' => [
                    'degree'            => 'Diplôme',
                    'availability-date' => 'Date de disponibilité',
                ],
            ],

            'department' => [
                'title' => 'Département',
            ],

            'salary' => [
                'title' => 'Salaire attendu et proposé',

                'entries' => [
                    'expected-salary'       => 'Salaire attendu',
                    'salary-proposed-extra' => 'Autre avantage',
                    'proposed-salary'       => 'Salaire proposé',
                    'salary-expected-extra' => 'Autre avantage',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Source et support',

                'entries' => [
                    'source' => 'Source',
                    'medium' => 'Support',
                ],
            ],
        ],
    ],
];
