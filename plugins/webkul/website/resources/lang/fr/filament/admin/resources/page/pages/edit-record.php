<?php

return [
    'notification' => [
        'title' => 'Page mise à jour',
        'body'  => 'La page a été mise à jour avec succès.',
    ],

    'header-actions' => [
        'draft' => [
            'label' => 'Définir comme brouillon',

            'notification' => [
                'title' => 'Page définie comme brouillon',
                'body'  => 'La page a été définie comme brouillon avec succès.',
            ],
        ],

        'publish' => [
            'label' => 'Publier',

            'notification' => [
                'title' => 'Page publiée',
                'body'  => 'La page a été publiée avec succès.',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Page supprimée',
                'body'  => 'La page a été supprimée avec succès.',
            ],
        ],
    ],
];
