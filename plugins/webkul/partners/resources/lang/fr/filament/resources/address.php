<?php

return [
    'form' => [
        'partner' => 'Partenaire',
        'name'    => 'Nom',
        'email'   => 'E-mail',
        'phone'   => 'Téléphone',
        'mobile'  => 'Mobile',
        'type'    => 'Type',
        'address' => 'Adresse',
        'city'    => 'Ville',
        'street1' => 'Rue 1',
        'street2' => 'Rue 2',
        'state'   => 'Région',
        'zip'     => 'Code postal',
        'code'    => 'Code',
        'country' => 'Pays',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ajouter une adresse',

                'notification' => [
                    'title' => 'Adresse créée',
                    'body'  => 'L\'adresse a été créée avec succès.',
                ],
            ],
        ],

        'columns' => [
            'type'    => 'Type',
            'name'    => 'Nom du contact',
            'address' => 'Adresse',
            'city'    => 'Ville',
            'street1' => 'Rue 1',
            'street2' => 'Rue 2',
            'state'   => 'Région',
            'zip'     => 'Code postal',
            'country' => 'Pays',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Adresse mise à jour',
                    'body'  => 'L\'adresse a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Adresse supprimée',
                    'body'  => 'L\'adresse a été supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Adresses supprimées',
                    'body'  => 'Les adresses ont été supprimées avec succès.',
                ],
            ],
        ],
    ],
];
