<?php

return [
    'title' => 'Jours obligatoires',

    'model-label' => 'Jour obligatoire',

    'navigation' => [
        'title' => 'Jours fériés obligatoires',
    ],

    'form' => [
        'fields' => [
            'name'       => 'Nom',
            'start-date' => 'Date de début',
            'end-date'   => 'Date de fin',
            'color'      => 'Couleur',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nom',
            'company-name' => 'Nom de la société',
            'created-by'   => 'Créé par',
            'start-date'   => 'Date de début',
            'end-date'     => 'Date de fin',
        ],

        'filters' => [
            'name'         => 'Nom',
            'company-name' => 'Nom de la société',
            'created-by'   => 'Créé par',
            'start-date'   => 'Date de début',
            'end-date'     => 'Date de fin',
        ],

        'groups' => [
            'name'         => 'Nom',
            'company-name' => 'Nom de la société',
            'created-by'   => 'Créé par',
            'start-date'   => 'Date de début',
            'end-date'     => 'Date de fin',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Jour obligatoire mis à jour',
                    'body'  => 'Le jour obligatoire a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jour obligatoire supprimé',
                    'body'  => 'Le jour obligatoire a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Jours obligatoires supprimés',
                    'body'  => 'Les jours obligatoires ont été supprimés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'       => 'Nom',
            'start-date' => 'Date de début',
            'end-date'   => 'Date de fin',
            'color'      => 'Couleur',
        ],
    ],
];
