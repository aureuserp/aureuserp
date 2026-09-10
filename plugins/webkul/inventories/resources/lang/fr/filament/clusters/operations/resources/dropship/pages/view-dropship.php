<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimer',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Livraison directe supprimée',
                    'body'  => 'La livraison directe a été supprimée avec succès.',
                ],

                'error' => [
                    'title' => 'La livraison directe n\'a pas pu être supprimée',
                    'body'  => 'La livraison directe ne peut pas être supprimée car elle est actuellement utilisée.',
                ],
            ],
        ],
    ],
];
