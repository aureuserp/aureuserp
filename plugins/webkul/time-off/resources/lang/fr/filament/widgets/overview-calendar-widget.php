<?php

return [
    'heading' => [
        'title' => 'Aperçu des congés',
    ],

    'modal-actions' => [
        'edit' => [
            'title'        => 'Modifier',
            'notification' => [
                'title' => 'Congé mis à jour',
                'body'  => 'La demande de congé a été mise à jour avec succès.',
            ],
        ],

        'delete' => [
            'title' => 'Supprimer',
        ],
    ],

    'view-action' => [
        'title'       => 'Voir',
        'description' => 'Voir la demande de congé',
    ],

    'header-actions' => [
        'create' => [
            'title'       => 'Nouveau congé',
            'description' => 'Créer une demande de congé',

            'notification' => [
                'title' => 'Congé créé',
                'body'  => 'La demande de congé a été créée avec succès.',
            ],

            'employee-not-found' => [
                'notification' => [
                    'title' => 'Employé introuvable',
                    'body'  => 'Veuillez ajouter un employé à votre profil avant de créer une demande de congé.',
                ],
            ],
        ],
    ],

    'form' => [
        'fields' => [
            'time-off-type'     => 'Type de congé',
            'request-date-from' => 'Date de demande du',
            'request-date-to'   => 'Date de demande au',
            'period'            => 'Période',
            'half-day'          => 'Demi-journée',
            'requested-days'    => 'Demandé (Jours/Heures)',
            'description'       => 'Description',
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type'           => 'Type de congé',
            'request-date-from'       => 'Date de demande du',
            'request-date-to'         => 'Date de demande au',
            'description'             => 'Description',
            'description-placeholder' => 'Aucune description fournie',
            'duration'                => 'Durée',
            'status'                  => 'Statut',
        ],
    ],
];
