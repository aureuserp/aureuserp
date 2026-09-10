<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimer',

            'actions' => [
                'without-content' => [
                    'label' => 'Imprimer le code-barres',
                ],

                'with-content' => [
                    'label' => 'Imprimer le code-barres avec contenu',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Colis supprimé',
                    'body'  => 'Le colis a été supprimé avec succès.',
                ],

                'error' => [
                    'title' => 'Le colis n\'a pas pu être supprimé',
                    'body'  => 'Le colis ne peut pas être supprimé car il est actuellement utilisé.',
                ],
            ],
        ],
    ],
];
