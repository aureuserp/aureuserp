<?php

return [
    'title' => 'Utilisateurs',

    'navigation' => [
        'title' => 'Utilisateurs',
    ],

    'global-search' => [
        'email' => 'E-mail',
    ],

    'form' => [
        'validation' => [
            'cannot-remove-last-admin'   => 'Impossible de retirer le rôle administrateur du dernier utilisateur administrateur.',
            'first-user-must-be-admin'   => 'Le premier utilisateur du système doit se voir attribuer un rôle administrateur.',
        ],

        'sections' => [
            'general-information' => [
                'title'  => 'Informations générales',
                'fields' => [
                    'name'                  => 'Nom',
                    'email'                 => 'E-mail',
                    'password'              => 'Mot de passe',
                    'password-confirmation' => 'Confirmation du mot de passe',
                ],
            ],

            'permissions' => [
                'title'  => 'Autorisations',
                'fields' => [
                    'roles'                                    => 'Rôles',
                    'permissions'                              => 'Autorisations',
                    'resource-permission'                      => 'Autorisation de ressource',
                    'resource-permission-self-change-disabled' => 'Vous ne pouvez pas modifier votre propre autorisation de ressource. Demandez à un autre administrateur de la mettre à jour.',
                    'teams'                                    => 'Équipes',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'  => 'Langue et statut',
                'fields' => [
                    'language' => 'Langue préférée',
                    'status'   => 'Statut',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Multi-société',
                'allowed-companies'           => 'Sociétés autorisées',
                'default-company'             => 'Société par défaut',
                'default-company-not-allowed' => 'La société par défaut doit faire partie des sociétés autorisées.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'avatar'              => 'Avatar',
            'name'                => 'Nom',
            'email'               => 'E-mail',
            'teams'               => 'Équipes',
            'role'                => 'Rôle',
            'resource-permission' => 'Autorisation de ressource',
            'default-company'     => 'Société par défaut',
            'allowed-company'     => 'Société autorisée',
            'created-by'          => 'Créé par',
            'created-at'          => 'Créé le',
            'updated-at'          => 'Mis à jour le',
        ],

        'filters' => [
            'resource-permission' => 'Autorisation de ressource',
            'teams'               => 'Équipes',
            'roles'               => 'Rôles',
            'default-company'     => 'Société par défaut',
            'allowed-companies'   => 'Sociétés autorisées',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Utilisateur modifié',
                    'body'  => 'L’utilisateur a été modifié avec succès.',
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

            'restore' => [
                'notification' => [
                    'title' => 'Utilisateur restauré',
                    'body'  => 'L’utilisateur a été restauré avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Utilisateurs restaurés',
                    'body'  => 'Les utilisateurs ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Utilisateurs supprimés',
                    'body'  => 'Les utilisateurs ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Utilisateurs définitivement supprimés',
                    'body'  => 'Les utilisateurs ont été définitivement supprimés avec succès.',
                    'error' => [
                        'title' => 'L’utilisateur n’a pas pu être supprimé',
                        'body'  => 'L’utilisateur ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Utilisateurs créés',
                    'body'  => 'Les utilisateurs ont été créés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title'   => 'Informations générales',
                'entries' => [
                    'name'                  => 'Nom',
                    'email'                 => 'E-mail',
                    'password'              => 'Mot de passe',
                    'password-confirmation' => 'Confirmation du mot de passe',
                ],
            ],

            'permissions' => [
                'title'   => 'Autorisations',
                'entries' => [
                    'roles'               => 'Rôles',
                    'permissions'         => 'Autorisations',
                    'resource-permission' => 'Autorisation de ressource',
                    'teams'               => 'Équipes',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'   => 'Langue et statut',
                'entries' => [
                    'language' => 'Langue préférée',
                    'status'   => 'Statut',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Multi-société',
                'allowed-companies'           => 'Sociétés autorisées',
                'default-company'             => 'Société par défaut',
                'default-company-not-allowed' => 'La société par défaut doit faire partie des sociétés autorisées.',
            ],
        ],
    ],
];
