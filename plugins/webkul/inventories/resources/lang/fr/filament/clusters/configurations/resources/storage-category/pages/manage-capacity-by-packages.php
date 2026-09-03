<?php

return [
    'title' => 'Capacité par colis',

    'form' => [
        'package-type' => 'Type de colis',
        'qty'          => 'Quantité',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ajouter une capacité de type de colis',

                'notification' => [
                    'title' => 'Capacité de type de colis créée',
                    'body'  => 'La capacité de type de colis a été ajoutée avec succès.',
                ],
            ],
        ],

        'columns' => [
            'package-type' => 'Type de colis',
            'qty'          => 'Quantité',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Capacité de type de colis mise à jour',
                    'body'  => 'La capacité de type de colis a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Capacité de type de colis supprimée',
                    'body'  => 'La capacité de type de colis a été supprimée avec succès.',
                ],
            ],
        ],
    ],
];
