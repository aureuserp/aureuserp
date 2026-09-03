<?php

return [
    'form' => [
        'fields' => [
            'code' => 'Code',
            'name' => 'Nom',
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Code',
            'name'       => 'Nom',
            'created-by' => 'Créé par',
        ],

        'groups' => [
            'code' => 'Code',
            'name' => 'Nom',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Incoterm mis à jour',
                    'body'  => 'L\'incoterm a été mis à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterm supprimé',
                    'body'  => 'L\'incoterm a été supprimé avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Incoterm restauré',
                    'body'  => 'L\'incoterm a été restauré avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Incoterms restaurés',
                    'body'  => 'Les incoterms ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterms supprimés',
                    'body'  => 'Les incoterms ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Incoterms définitivement supprimés',
                    'body'  => 'Les incoterms ont été définitivement supprimés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nom',
            'code' => 'Code',
        ],
    ],
];
