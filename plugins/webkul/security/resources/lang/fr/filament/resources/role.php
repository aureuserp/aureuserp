<?php

return [
    'permission-descriptions' => [
        'view_any'         => 'Lister et parcourir tous les enregistrements.',
        'view'             => 'Voir un enregistrement en détail.',
        'create'           => 'Créer de nouveaux enregistrements.',
        'update'           => 'Modifier et sauvegarder les enregistrements existants.',
        'delete'           => 'Supprimer un enregistrement.',
        'delete_any'       => 'Supprimer plusieurs enregistrements à la fois.',
        'force_delete'     => 'Supprimer définitivement un enregistrement (sans corbeille).',
        'force_delete_any' => 'Supprimer définitivement plusieurs enregistrements.',
        'restore'          => 'Restaurer un enregistrement archivé.',
        'restore_any'      => 'Restaurer plusieurs enregistrements archivés.',
        'reorder'          => 'Modifier l\'ordre de tri des enregistrements.',
    ],

    'form' => [
        'fields' => [
            'web'     => 'Web',
            'sanctum' => 'Sanctum',
        ],
    ],

    'notification' => [
        'system-role-delete' => [
            'title' => 'Le rôle système ne peut pas être supprimé',
            'body'  => 'Il s’agit d’un rôle système et il ne peut pas être supprimé.',
        ],
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Rôle supprimé',
                    'body'  => 'Le rôle a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Rôles supprimés',
                    'body'  => 'Les rôles sélectionnés ont été supprimés avec succès.',
                ],
            ],
        ],
    ],
];
