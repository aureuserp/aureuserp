<?php

return [
    'notification' => [
        'title' => 'Accord d\'achat mis à jour',
        'body'  => 'L\'accord d\'achat a été mis à jour avec succès.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Confirmer',

            'notification' => [
                'unable' => [
                    'title' => 'Impossible de confirmer l\'accord d\'achat',
                    'body'  => 'Ajoutez au moins une ligne de produit avant de confirmer cet accord d\'achat.',
                ],
            ],
        ],

        'close' => [
            'label'        => 'Fermer',
            'notification' => [
                'warning' => [
                    'title' => 'Impossible de fermer l\'accord d\'achat',
                    'body'  => 'Vous ne pouvez pas fermer cet accord d\'achat car certaines demandes de prix associées ne sont pas au statut Terminé ou Annulé.',
                ],
            ],
        ],

        'cancel' => [
            'label' => 'Annuler',
        ],

        'print' => [
            'label' => 'Imprimer',
        ],

        'delete' => [
            'notification' => [
                'title' => 'Accord d\'achat supprimé',
                'body'  => 'L\'accord d\'achat a été supprimé avec succès.',
            ],
        ],
    ],
];
