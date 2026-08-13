<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Taxe supprimée',
                    'body'  => 'La taxe a été supprimée avec succès.',
                ],

                'error' => [
                    'title' => 'La taxe n\'a pas pu être supprimée',
                    'body'  => 'La taxe ne peut pas être supprimée car elle est actuellement utilisée.',
                ],
            ],
        ],
    ],
];
