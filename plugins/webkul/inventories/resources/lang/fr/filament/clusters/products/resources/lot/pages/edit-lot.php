<?php

return [
    'notification' => [
        'title' => 'Lot mis à jour',
        'body'  => 'Le lot a été mis à jour avec succès.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Imprimer',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Lot supprimé',
                    'body'  => 'Le lot a été supprimé avec succès.',
                ],

                'error' => [
                    'title' => 'Le lot n\'a pas pu être supprimé',
                    'body'  => 'Le lot ne peut pas être supprimé car il est actuellement utilisé.',
                ],
            ],
        ],
    ],
];
