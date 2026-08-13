<?php

return [
    'notification' => [
        'title'              => 'Congé mis à jour',
        'body'               => 'Le congé a été mis à jour avec succès.',
        'action_not_allowed' => [
            'title' => 'Action non autorisée',
            'body'  => 'Vous ne pouvez pas modifier cette demande de congé car elle est dans un état verrouillé.',
        ],
        'overlap' => [
            'title' => 'Demande de congé en chevauchement',
            'body'  => 'Les dates de congé sélectionnées chevauchent une demande existante. Veuillez choisir des dates différentes.',
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Congé supprimé',
                'body'  => 'Le congé a été supprimé avec succès.',
            ],
        ],
    ],
];
