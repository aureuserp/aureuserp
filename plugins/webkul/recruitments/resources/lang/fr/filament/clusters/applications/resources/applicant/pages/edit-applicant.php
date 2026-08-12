<?php

return [
    'create-employee' => 'Créer un employé',
    'goto-employee'   => 'Aller à l\'employé',

    'notification' => [
        'title' => 'Candidat mis à jour',
        'body'  => 'Le candidat a été mis à jour avec succès.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Candidat supprimé',
                'body'  => 'Le candidat a été supprimé avec succès.',
            ],
        ],
        'force-delete' => [
            'notification' => [
                'title' => 'Candidat supprimé',
                'body'  => 'Le candidat a été définitivement supprimé avec succès.',
            ],
        ],

        'refuse' => [
            'title'        => 'Motif de refus',
            'form'         => [
                'fields' => [
                    'notify'   => 'Notifier',
                    'email-to' => 'E-mail à',
                ],
            ],
            'notification' => [
                'title' => 'Candidat refusé',
                'body'  => 'Le candidat a été refusé avec succès.',
            ],
        ],

        'reopen' => [
            'title'        => 'Rouvrir le candidat',
            'notification' => [
                'title' => 'Candidat rouvert',
                'body'  => 'Le candidat a été rouvert avec succès.',
            ],
        ],

        'state' => [
            'notification' => [
                'title' => 'État du candidat mis à jour',
                'body'  => 'L\'état du candidat a été mis à jour avec succès.',
            ],
        ],
    ],

    'mail' => [
        'application-refused' => [
            'subject' => 'Votre candidature : :application',
        ],

        'application-confirm' => [
            'subject' => 'Votre candidature : :job_position',
        ],
        'interviewer-assigned' => [
            'subject' => 'Vous avez été assigné(e) au candidat :applicant.',
        ],
    ],
];
