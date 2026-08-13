<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimer',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Livraison supprimée',
                    'body'  => 'La livraison a été supprimée avec succès.',
                ],

                'error' => [
                    'title' => 'La livraison n\'a pas pu être supprimée',
                    'body'  => 'La livraison ne peut pas être supprimée car elle est actuellement utilisée.',
                ],
            ],
        ],
    ],
];
