<?php

return [
    'notification' => [
        'title' => 'Commande mise à jour',
        'body'  => 'La commande a été mise à jour avec succès.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Confirmer',
        ],

        'close' => [
            'label' => 'Fermer',
        ],

        'cancel' => [
            'label' => 'Annuler',
        ],

        'print' => [
            'label' => 'Imprimer',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Commande supprimée',
                    'body'  => 'La commande a été supprimée avec succès.',
                ],

                'error' => [
                    'title' => 'La commande n\'a pas pu être supprimée',
                    'body'  => 'La commande ne peut pas être supprimée car elle est actuellement utilisée.',
                ],
            ],
        ],
    ],
];
