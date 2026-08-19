<?php

return [
    'navigation' => [
        'title' => 'Équipes',
    ],

    'form' => [
        'name'    => 'Nom',
        'company' => 'Société',
        'users'   => 'Membres de l\'équipe',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'company'    => 'Société',
            'users'      => 'Membres de l\'équipe',
            'created-at' => 'Créé le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Équipe mise à jour',
                    'body'  => 'L\'équipe a été mise à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Équipe restaurée',
                    'body'  => 'L\'équipe a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Équipe supprimée',
                    'body'  => 'L\'équipe a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Équipe supprimée définitivement',
                        'body'  => 'L\'équipe a été supprimée définitivement avec succès.',
                    ],
                    'error' => [
                        'title' => 'L\'équipe n\'a pas pu être supprimée définitivement',
                        'body'  => 'L\'équipe est utilisée et ne peut pas être supprimée définitivement.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Équipes restaurées',
                    'body'  => 'Les équipes ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Équipes supprimées',
                    'body'  => 'Les équipes ont été supprimées avec succès.',
                ],
            ],
        ],
    ],
];
