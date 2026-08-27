<?php

return [
    'title' => 'Voir la devise',

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Devise supprimée',
                'body'  => 'La devise a été supprimée avec succès.',

                'error' => [
                    'title' => 'Impossible de supprimer la devise',
                    'body'  => 'La devise ne peut pas être supprimée car elle est actuellement utilisée.',
                ],
            ],
        ],
    ],
];
