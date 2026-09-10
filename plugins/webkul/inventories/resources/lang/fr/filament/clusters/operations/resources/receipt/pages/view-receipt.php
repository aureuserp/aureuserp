<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimer',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Réception supprimée',
                    'body'  => 'La réception a été supprimée avec succès.',
                ],

                'error' => [
                    'title' => 'La réception n\'a pas pu être supprimée',
                    'body'  => 'La réception ne peut pas être supprimée car elle est actuellement utilisée.',
                ],
            ],
        ],
    ],
];
