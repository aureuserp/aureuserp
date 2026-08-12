<?php

return [
    'title' => 'Sociétés',

    'navigation' => [
        'title' => 'Sociétés',
    ],

    'global-search' => [
        'email' => 'E-mail',
    ],

    'form' => [
        'sections' => [
            'company-information' => [
                'title'  => 'Informations sur la société',
                'fields' => [
                    'name'                  => 'Nom de la société',
                    'registration-number'   => "Numéro d'immatriculation",
                    'company-id'            => 'ID de la société',
                    'tax-id'                => 'Numéro fiscal',
                    'tax-id-tooltip'        => "Le numéro fiscal est un identifiant unique pour votre société.",
                    'website'               => 'Site web',
                ],
            ],

            'address-information' => [
                'title'  => "Informations sur l'adresse",

                'fields' => [
                    'street1'        => 'Rue 1',
                    'street2'        => 'Rue 2',
                    'city'           => 'Ville',
                    'zipcode'        => 'Code postal',
                    'country'        => 'Pays',
                    'currency-name'  => 'Nom de la devise',
                    'phone-code'     => 'Indicatif téléphonique',
                    'code'           => 'Code',
                    'country-name'   => 'Nom du pays',
                    'state-required' => 'État requis',
                    'zip-required'   => 'Code postal requis',
                    'create-country' => 'Créer un pays',
                    'state'          => 'État',
                    'state-name'     => "Nom de l'état",
                    'state-code'     => "Code de l'état",
                    'create-state'   => 'Créer un état',
                ],
            ],

            'additional-information' => [
                'title' => 'Informations complémentaires',

                'fields' => [
                    'default-currency'        => 'Devise par défaut',
                    'currency-name'           => 'Nom de la devise',
                    'currency-full-name'      => 'Nom complet de la devise',
                    'currency-symbol'         => 'Symbole de la devise',
                    'currency-iso-numeric'    => 'Code ISO numérique de la devise',
                    'currency-decimal-places' => 'Décimales de la devise',
                    'currency-rounding'       => 'Arrondi de la devise',
                    'currency-status'         => 'Statut de la devise',
                    'company-foundation-date' => 'Date de création de la société',
                    'currency-create'         => 'Créer une devise',
                    'status'                  => 'Statut',
                ],
            ],

            'branding' => [
                'title'  => 'Image de marque',
                'fields' => [
                    'company-logo' => 'Logo de la société',
                    'color'        => 'Couleur',
                ],
            ],

            'contact-information' => [
                'title'  => 'Informations de contact',
                'fields' => [
                    'email'  => 'Adresse e-mail',
                    'phone'  => 'Numéro de téléphone',
                    'mobile' => 'Numéro de téléphone',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Logo',
            'company-name'         => 'Nom de la société',
            'branches'             => 'Filiales',
            'email'                => 'E-mail',
            'city'                 => 'Ville',
            'country'              => 'Pays',
            'currency'             => 'Devise',
            'status'               => 'Statut',
            'created-at'           => 'Créé le',
            'updated-at'           => 'Mis à jour le',
        ],

        'groups' => [
            'company-name' => 'Nom de la société',
            'city'         => 'Ville',
            'country'      => 'Pays',
            'state'        => 'État',
            'email'        => 'E-mail',
            'phone'        => 'Téléphone',
            'currency'     => 'Devise',
            'created-at'   => 'Créé le',
            'updated-at'   => 'Mis à jour le',
        ],

        'filters' => [
            'status'  => 'Statut',
            'country' => 'Pays',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Société modifiée',
                    'body'  => 'La société a été modifiée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Société supprimée',
                    'body'  => 'La société a été supprimée avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Société restaurée',
                    'body'  => 'La société a été restaurée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Sociétés restaurées',
                    'body'  => 'Les sociétés ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sociétés supprimées',
                    'body'  => 'Les sociétés ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Sociétés définitivement supprimées',
                    'body'  => 'Les sociétés ont été définitivement supprimées avec succès.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Sociétés créées',
                    'body'  => 'Les sociétés ont été créées avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'company-information' => [
                'title'   => 'Informations sur la société',
                'entries' => [
                    'name'                  => 'Nom de la société',
                    'registration-number'   => "Numéro d'immatriculation",
                    'company-id'            => 'ID de la société',
                    'tax-id'                => 'Numéro fiscal',
                    'tax-id-tooltip'        => "Le numéro fiscal est un identifiant unique pour votre société.",
                    'website'               => 'Site web',
                ],
            ],

            'address-information' => [
                'title'  => "Informations sur l'adresse",

                'entries' => [
                    'street1'        => 'Rue 1',
                    'street2'        => 'Rue 2',
                    'city'           => 'Ville',
                    'zipcode'        => 'Code postal',
                    'country'        => 'Pays',
                    'currency-name'  => 'Nom de la devise',
                    'phone-code'     => 'Indicatif téléphonique',
                    'code'           => 'Code',
                    'country-name'   => 'Nom du pays',
                    'state-required' => 'État requis',
                    'zip-required'   => 'Code postal requis',
                    'create-country' => 'Créer un pays',
                    'state'          => 'État',
                    'state-name'     => "Nom de l'état",
                    'state-code'     => "Code de l'état",
                    'create-state'   => 'Créer un état',
                ],
            ],

            'additional-information' => [
                'title' => 'Informations complémentaires',

                'entries' => [
                    'default-currency'        => 'Devise par défaut',
                    'currency-name'           => 'Nom de la devise',
                    'currency-full-name'      => 'Nom complet de la devise',
                    'currency-symbol'         => 'Symbole de la devise',
                    'currency-iso-numeric'    => 'Code ISO numérique de la devise',
                    'currency-decimal-places' => 'Décimales de la devise',
                    'currency-rounding'       => 'Arrondi de la devise',
                    'currency-status'         => 'Statut de la devise',
                    'company-foundation-date' => 'Date de création de la société',
                    'currency-create'         => 'Créer une devise',
                    'status'                  => 'Statut',
                ],
            ],

            'branding' => [
                'title'   => 'Image de marque',
                'entries' => [
                    'company-logo' => 'Logo de la société',
                    'color'        => 'Couleur',
                ],
            ],

            'contact-information' => [
                'title'   => 'Informations de contact',
                'entries' => [
                    'email'  => 'Adresse e-mail',
                    'phone'  => 'Numéro de téléphone',
                    'mobile' => 'Numéro de téléphone',
                ],
            ],
        ],
    ],
];
