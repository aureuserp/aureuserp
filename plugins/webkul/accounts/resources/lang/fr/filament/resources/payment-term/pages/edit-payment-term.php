<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Condition de paiement mise à jour',
            'body'  => 'La condition de paiement a été mise à jour avec succès.',
        ],

        'validation-error' => [
            'title' => 'Erreur de validation',
            'body'  => 'Le terme d\'échéance doit comporter au moins une ligne de pourcentage et la somme des pourcentages doit être égale à 100 %.',
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Condition de paiement supprimée',
                'body'  => 'La condition de paiement a été supprimée avec succès.',
            ],
        ],
    ],
];
