<?php

return [
    'notification' => [
        'title' => 'Utilisateur mis à jour',
        'body'  => 'L’utilisateur a été mis à jour avec succès.',
    ],

    'header-actions' => [
        'change-password' => [
            'label' => 'Changer le mot de passe',

            'notification' => [
                'title' => 'Mot de passe modifié',
                'body'  => 'Le mot de passe a été modifié avec succès.',
            ],

            'form' => [
                'new-password'         => 'Nouveau mot de passe',
                'confirm-new-password' => 'Confirmer le nouveau mot de passe',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Utilisateur supprimé',
                'body'  => 'L’utilisateur a été supprimé avec succès.',
                'error' => [
                    'title' => 'L’utilisateur ne peut pas être supprimé',
                    'body'  => 'Il s’agit d’un utilisateur par défaut ou vous ne pouvez pas vous supprimer vous-même.',
                ],
            ],
        ],
    ],
];
