<?php

return [
    'title'      => 'Caisses',

    'navigation' => [
        'label' => 'Caisses',
    ],

    'session' => [
        'open'   => 'Ouverte par :name',
        'closed' => 'Fermée',
    ],

    'actions' => [
        'open'    => 'Ouvrir la caisse',
        'resume'  => 'Reprendre',

        'discard' => [
            'label'        => 'Abandonner la session',
            'heading'      => 'Abandonner cette session ?',
            'description'  => 'La caisse a été ouverte mais rien n\'y a été vendu. L\'abandon supprime la session afin qu\'elle puisse être rouverte proprement.',
            'confirm'      => 'Abandonner',

            'notification' => [
                'title' => 'Session abandonnée',
            ],
        ],
    ],

    'empty' => [
        'heading'     => 'Aucun point de vente disponible',
        'description' => 'Créez un point de vente actif dans le back-office pour commencer à vendre.',
    ],
];
