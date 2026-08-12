<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Catégorie supprimée',
                    'body'  => 'La catégorie a été supprimée avec succès.',
                ],

                'error' => [
                    'title' => 'La catégorie n\'a pas pu être supprimée',
                    'body'  => 'La catégorie ne peut pas être supprimée car elle est actuellement utilisée.',
                ],
            ],
        ],
    ],
];
