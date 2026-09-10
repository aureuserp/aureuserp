<?php

return [
    'form' => [
        'fields' => [
            'tax-source'      => 'Taxe source',
            'tax-destination' => 'Taxe de destination',
        ],
    ],

    'table' => [
        'columns' => [
            'tax-source'      => 'Taxe source',
            'tax-destination' => 'Taxe de destination',
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

    'infolist' => [
        'entries' => [
            'tax-source'      => 'Taxe source',
            'tax-destination' => 'Taxe de destination',
        ],
    ],
];
