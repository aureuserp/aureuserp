<?php

return [
    'form' => [
        'name'       => 'Nom',
        'short-name' => 'Nom court',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'short-name' => 'Nom court',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'filters' => [
            'creator' => 'Créateur',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Titre mis à jour',
                    'body'  => 'Le titre a été mis à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Titre supprimé',
                    'body'  => 'Le titre a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Titres supprimés',
                    'body'  => 'Les titres ont été supprimés avec succès.',
                ],
            ],
        ],
    ],
];
