<?php

return [
    'form' => [
        'value'                  => 'Valeur',
        'due'                    => 'Échéance',
        'delay-due'              => 'Délai d\'échéance',
        'delay-type'             => 'Type de délai',
        'days-on-the-next-month' => 'Jours du mois suivant',
        'days'                   => 'Jours',
        'payment-term'           => 'Condition de paiement',
    ],

    'table' => [
        'columns' => [
            'due'          => 'Échéance',
            'value'        => 'Valeur',
            'value-amount' => 'Montant de la valeur',
            'after'        => 'Après',
            'delay-type'   => 'Type de délai',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Condition de paiement mise à jour',
                    'body'  => 'La condition de paiement a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Condition de paiement supprimée',
                    'body'  => 'La condition de paiement a été supprimée avec succès.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Condition de paiement créée',
                    'body'  => 'La condition de paiement a été créée avec succès.',
                ],
            ],
        ],
    ],
];
