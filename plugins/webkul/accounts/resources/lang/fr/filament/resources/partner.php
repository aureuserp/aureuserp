<?php

return [
    'title' => 'Partenaires',

    'navigation' => [
        'title' => 'Partenaires',
    ],

    'form' => [
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Ventes',

                        'fields' => [
                            'sales-person'   => 'Vendeur',
                            'payment-terms'  => 'Conditions de paiement',
                            'payment-method' => 'Mode de paiement',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Achat',

                        'fields' => [
                            'payment-terms'  => 'Conditions de paiement',
                            'payment-method' => 'Mode de paiement',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Informations fiscales',

                        'fields' => [
                            'fiscal-position'    => 'Position fiscale',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Facturation',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Factures clients',

                        'fields' => [
                            'invoice-sending-method'   => 'Méthode d\'envoi de facture',
                            'invoice-edi-format-store' => 'Format de facture électronique',
                            'peppol-eas'               => 'Adresse Peppol',
                            'endpoint'                 => 'Point de terminaison',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Écritures comptables',

                        'fields' => [
                            'account-receivable' => 'Compte à recevoir',
                            'account-payable'    => 'Compte à payer',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Automatisation',

                        'fields' => [
                            'auto-post-bills'                => 'Comptabilisation automatique des factures fournisseurs',
                            'ignore-abnormal-invoice-amount' => 'Ignorer le montant anormal de la facture',
                            'ignore-abnormal-invoice-date'   => 'Ignorer la date anormale de la facture',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Notes internes',
            ],
        ],
    ],

    'infolist' => [

        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Ventes',

                        'entries' => [
                            'sales-person'   => 'Vendeur',
                            'payment-terms'  => 'Conditions de paiement',
                            'payment-method' => 'Mode de paiement',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Achat',

                        'entries' => [
                            'payment-terms'  => 'Conditions de paiement',
                            'payment-method' => 'Mode de paiement',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Informations fiscales',

                        'entries' => [
                            'fiscal-position'    => 'Position fiscale',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Facturation',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Factures clients',

                        'entries' => [
                            'invoice-sending-method'   => 'Méthode d\'envoi de facture',
                            'invoice-edi-format-store' => 'Format de facture électronique',
                            'peppol-eas'               => 'Adresse Peppol',
                            'endpoint'                 => 'Point de terminaison',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Écritures comptables',

                        'entries' => [
                            'account-receivable' => 'Compte à recevoir',
                            'account-payable'    => 'Compte à payer',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Automatisation',

                        'entries' => [
                            'auto-post-bills'                => 'Comptabilisation automatique des factures fournisseurs',
                            'ignore-abnormal-invoice-amount' => 'Ignorer le montant anormal de la facture',
                            'ignore-abnormal-invoice-date'   => 'Ignorer la date anormale de la facture',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Notes internes',
            ],
        ],
    ],
];
