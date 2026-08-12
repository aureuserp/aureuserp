<?php

return [
    'notification' => [
        'title' => 'Livraison mise à jour',
        'body'  => 'La livraison a été mise à jour avec succès.',
    ],

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
