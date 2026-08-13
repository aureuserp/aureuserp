<?php

return [
    'form' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Informations générales',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Informations sur la filiale',

                        'fields' => [
                            'company-name'                => 'Nom de la société',
                            'registration-number'         => "Numéro d'immatriculation",
                            'tax-id'                      => 'Numéro fiscal',
                            'tax-id-tooltip'              => "Le numéro fiscal est un identifiant unique pour votre société.",
                            'color'                       => 'Couleur',
                            'company-id'                  => 'ID de la société',
                            'company-id-tooltip'          => "L'ID de la société est un identifiant unique pour votre société.",
                        ],
                    ],

                    'branding' => [
                        'title'  => 'Image de marque',
                        'fields' => [
                            'branch-logo' => 'Logo de la filiale',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => "Informations sur l'adresse",

                'sections' => [
                    'address-information' => [
                        'title' => "Informations sur l'adresse",

                        'fields' => [
                            'street1'                => 'Rue 1',
                            'street2'                => 'Rue 2',
                            'city'                   => 'Ville',
                            'zip'                    => 'Code postal',
                            'country'                => 'Pays',
                            'country-currency-name'  => 'Nom de la devise',
                            'country-phone-code'     => 'Indicatif téléphonique',
                            'country-code'           => 'Code',
                            'country-name'           => 'Nom du pays',
                            'country-state-required' => 'État requis',
                            'country-zip-required'   => 'Code postal requis',
                            'country-create'         => 'Créer un pays',
                            'state'                  => 'État',
                            'state-name'             => "Nom de l'état",
                            'state-code'             => "Code de l'état",
                            'zip-code'               => 'Code postal',
                            'state-create'           => 'Créer un état',
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
                            'currency-create'         => 'Créer une devise',
                            'company-foundation-date' => 'Date de création de la société',
                            'status'                  => 'Statut',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Informations de contact',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Informations de contact',

                        'fields' => [
                            'email-address' => 'Adresse e-mail',
                            'phone-number'  => 'Numéro de téléphone',
                            'mobile-number' => 'Numéro de téléphone',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Logo',
            'company-name'         => 'Nom de la filiale',
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
            'company-name' => 'Nom de la filiale',
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
            'trashed' => 'Corbeille',
            'status'  => 'Statut',
            'country' => 'Pays',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Filiale créée',
                    'body'  => 'La filiale a été créée avec succès.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Filiale mise à jour',
                    'body'  => 'La filiale a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Filiale supprimée',
                    'body'  => 'La filiale a été supprimée avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Filiale restaurée',
                    'body'  => 'La filiale a été restaurée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Filiales restaurées',
                    'body'  => 'Les filiales ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Filiales supprimées',
                    'body'  => 'Les filiales ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Filiales définitivement supprimées',
                    'body'  => 'Les filiales ont été définitivement supprimées avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Informations générales',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Informations sur la filiale',

                        'entries' => [
                            'company-name'                => 'Nom de la société',
                            'registration-number'         => "Numéro d'immatriculation",
                            'tax-id'                      => 'Numéro fiscal',
                            'registration-number-tooltip' => "Le numéro fiscal est un identifiant unique pour votre société.",
                            'color'                       => 'Couleur',
                        ],
                    ],

                    'branding' => [
                        'title'   => 'Image de marque',
                        'entries' => [
                            'branch-logo' => 'Logo de la filiale',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => "Informations sur l'adresse",

                'sections' => [
                    'address-information' => [
                        'title' => "Informations sur l'adresse",

                        'entries' => [
                            'street1'                => 'Rue 1',
                            'street2'                => 'Rue 2',
                            'city'                   => 'Ville',
                            'zip'                    => 'Code postal',
                            'country'                => 'Pays',
                            'country-currency-name'  => 'Nom de la devise',
                            'country-phone-code'     => 'Indicatif téléphonique',
                            'country-code'           => 'Code',
                            'country-name'           => 'Nom du pays',
                            'country-state-required' => 'État requis',
                            'country-zip-required'   => 'Code postal requis',
                            'country-create'         => 'Créer un pays',
                            'state'                  => 'État',
                            'state-name'             => "Nom de l'état",
                            'state-code'             => "Code de l'état",
                            'zip-code'               => 'Code postal',
                            'state-create'           => 'Créer un état',
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
                            'currency-create'         => 'Créer une devise',
                            'company-foundation-date' => 'Date de création de la société',
                            'status'                  => 'Statut',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Informations de contact',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Informations de contact',

                        'entries' => [
                            'email-address' => 'Adresse e-mail',
                            'phone-number'  => 'Numéro de téléphone',
                            'mobile-number' => 'Numéro de téléphone',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
