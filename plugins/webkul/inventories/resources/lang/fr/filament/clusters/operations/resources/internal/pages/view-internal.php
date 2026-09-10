<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimer',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Transfert interne supprimé',
                    'body'  => 'Le transfert interne a été supprimé avec succès.',
                ],

                'error' => [
                    'title' => 'Le transfert interne n\'a pas pu être supprimé',
                    'body'  => 'Le transfert interne ne peut pas être supprimé car il est actuellement utilisé.',
                ],
            ],
        ],
    ],
];
