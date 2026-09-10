<?php

return [
    'notification' => [
        'title' => 'Article mis à jour',
        'body'  => 'L\'article a été mis à jour avec succès.',
    ],

    'header-actions' => [
        'draft' => [
            'label' => 'Définir comme brouillon',

            'notification' => [
                'title' => 'Article défini comme brouillon',
                'body'  => 'L\'article a été défini comme brouillon avec succès.',
            ],
        ],

        'publish' => [
            'label' => 'Publier',

            'notification' => [
                'title' => 'Article publié',
                'body'  => 'L\'article a été publié avec succès.',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Article supprimé',
                'body'  => 'L\'article a été supprimé avec succès.',
            ],
        ],
    ],
];
