<?php

return [
    'notification' => [
        'title' => 'Groupe de taxes mis à jour',
        'body'  => 'Le groupe de taxes a été mis à jour avec succès.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Groupe de taxes supprimé',
                    'body'  => 'Le groupe de taxes a été supprimé avec succès.',
                ],

                'error' => [
                    'title' => 'Le groupe de taxes n\'a pas pu être supprimé',
                    'body'  => 'Le groupe de taxes ne peut pas être supprimé car il est actuellement utilisé.',
                ],
            ],
        ],
    ],
];
