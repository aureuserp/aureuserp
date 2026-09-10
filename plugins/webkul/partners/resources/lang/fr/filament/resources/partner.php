<?php

return [
    'global-search' => [
        'email' => 'E-mail',
        'phone' => 'Téléphone',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'company'    => 'Société',
                    'avatar'     => 'Avatar',
                    'tax-id'     => 'Numéro fiscal',
                    'job-title'  => 'Fonction',
                    'phone'      => 'Téléphone',
                    'mobile'     => 'Mobile',
                    'email'      => 'E-mail',
                    'website'    => 'Site web',
                    'title'      => 'Titre',
                    'name'       => 'Nom',
                    'short-name' => 'Nom court',
                    'tags'       => 'Étiquettes',
                    'color'      => 'Couleur',
                ],

                'address' => [
                    'title' => 'Adresse',

                    'fields' => [
                        'street1'  => 'Rue 1',
                        'street2'  => 'Rue 2',
                        'city'     => 'Ville',
                        'zip'      => 'Code postal',
                        'state'    => 'Région',
                        'country'  => 'Pays',
                        'name'     => 'Nom',
                        'code'     => 'Code',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Ventes et achats',

                'fields' => [
                    'responsible'           => 'Responsable',
                    'responsible-hint-text' => 'Il s\'agit du commercial interne responsable de ce client',
                    'company-id'            => 'ID société',
                    'company-id-hint-text'  => 'Le numéro d\'immatriculation de la société, utilisé s\'il diffère du numéro fiscal. Il doit être unique parmi tous les partenaires d\'un même pays.',
                    'reference'             => 'Référence',
                    'industry'              => 'Secteur d\'activité',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'parent'     => 'Parent',
            'work-email' => 'E-mail professionnel',
            'work-phone' => 'Téléphone professionnel',
        ],

        'groups' => [
            'account-type' => 'Type de compte',
            'parent'       => 'Parent',
            'title'        => 'Titre',
            'job-title'    => 'Fonction',
            'industry'     => 'Secteur d\'activité',
        ],

        'filters' => [
            'account-type'     => 'Type de compte',
            'name'             => 'Nom',
            'email'            => 'E-mail',
            'parent'           => 'Parent',
            'title'            => 'Titre',
            'tax-id'           => 'Numéro fiscal',
            'phone'            => 'Téléphone',
            'mobile'           => 'Mobile',
            'job-title'        => 'Fonction',
            'website'          => 'Site web',
            'company-registry' => 'Registre de la société',
            'responsible'      => 'Responsable',
            'reference'        => 'Référence',
            'parent'           => 'Parent',
            'creator'          => 'Créateur',
            'company'          => 'Société',
            'industry'         => 'Secteur d\'activité',
            'industry'         => 'Secteur d\'activité',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Contact mis à jour',
                    'body'  => 'Le contact a été mis à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Contact restauré',
                    'body'  => 'Le contact a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Contact supprimé',
                    'body'  => 'Le contact a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contact définitivement supprimé',
                        'body'  => 'Le contact a été définitivement supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'Le contact n\'a pas pu être supprimé',
                        'body'  => 'Le contact ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Contacts restaurés',
                    'body'  => 'Les contacts ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Contacts supprimés',
                    'body'  => 'Les contacts ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contacts définitivement supprimés',
                        'body'  => 'Les contacts ont été définitivement supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les contacts n\'ont pas pu être supprimés',
                        'body'  => 'Les contacts ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'company'    => 'Société',
                    'avatar'     => 'Avatar',
                    'tax-id'     => 'Numéro fiscal',
                    'job-title'  => 'Fonction',
                    'phone'      => 'Téléphone',
                    'mobile'     => 'Mobile',
                    'email'      => 'E-mail',
                    'website'    => 'Site web',
                    'title'      => 'Titre',
                    'name'       => 'Nom',
                    'short-name' => 'Nom court',
                    'tags'       => 'Étiquettes',
                ],

                'address' => [
                    'title' => 'Adresse',

                    'fields' => [
                        'street1'  => 'Rue 1',
                        'street2'  => 'Rue 2',
                        'city'     => 'Ville',
                        'zip'      => 'Code postal',
                        'state'    => 'Région',
                        'country'  => 'Pays',
                        'name'     => 'Nom',
                        'code'     => 'Code',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Ventes et achats',

                'fields' => [
                    'responsible'           => 'Responsable',
                    'responsible-hint-text' => 'Il s\'agit du commercial interne responsable de ce client',
                    'company-id'            => 'ID société',
                    'company-id-hint-text'  => 'Le numéro d\'immatriculation de la société. À utiliser s\'il diffère du numéro fiscal. Il doit être unique parmi tous les partenaires d\'un même pays',
                    'reference'             => 'Référence',
                    'industry'              => 'Secteur d\'activité',
                ],
            ],
        ],
    ],
];
