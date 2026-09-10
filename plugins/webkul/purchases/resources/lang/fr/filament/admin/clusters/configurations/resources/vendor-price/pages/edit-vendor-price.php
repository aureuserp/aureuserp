<?php

return [
    'navigation' => [
        'title' => 'Modifier la liste de prix fournisseur',
    ],

    'notification' => [
        'title' => 'Prix fournisseur mis à jour',
        'body'  => 'Le prix fournisseur a été mis à jour avec succès.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Prix fournisseur supprimé',
                    'body'  => 'Le prix fournisseur a été supprimé avec succès.',
                ],

                'error' => [
                    'title' => 'Le prix fournisseur n\'a pas pu être supprimé',
                    'body'  => 'Le prix fournisseur ne peut pas être supprimé car il est actuellement utilisé.',
                ],
            ],
        ],
    ],
];
