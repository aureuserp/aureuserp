<?php

return [
    'navigation' => [
        'title' => 'Rapports',
    ],
    'common' => [
        'from-to'                  => ':report - Du :from au :to',
        'expand-all'               => 'Tout développer',
        'collapse-all'             => 'Tout réduire',
        'account'                  => 'Compte',
        'date'                     => 'Date',
        'communication'            => 'Communication',
        'partner'                  => 'Partenaire',
        'journal'                  => 'Journal',
        'invoice-date'             => 'Date de facture',
        'due-date'                 => 'Date d\'échéance',
        'debit'                    => 'Débit',
        'credit'                   => 'Crédit',
        'balance'                  => 'Solde',
        'total'                    => 'Total',
        'opening-balance'          => 'Solde d\'ouverture',
        'initial-balance'          => 'Solde initial',
        'end-balance'              => 'Solde final',
        'not-due'                  => 'Non échu',
        'no-data'                  => 'Aucune donnée disponible',
        'no-accounts-transactions' => 'Aucun compte avec des transactions durant cette période',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'Bilan',
                'group' => 'Rapports de synthèse',
            ],
            'actions' => [
                'export-excel' => 'Exporter vers Excel',
                'export-pdf'   => 'Exporter vers PDF',
            ],
            'filters' => [
                'date-range' => 'Période',
                'journals'   => 'Journaux',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title'       => 'ACTIF',
                        'total-label' => 'Total ACTIF',
                        'subsections' => [
                            'current-assets' => [
                                'title'       => 'Actifs circulants',
                                'total-label' => 'Total actifs circulants',
                            ],
                            'fixed-assets' => [
                                'title'       => 'Immobilisations',
                                'total-label' => 'Total immobilisations',
                            ],
                            'non-current-assets' => [
                                'title'       => 'Actifs non courants',
                                'total-label' => 'Total actifs non courants',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title'       => 'PASSIF',
                        'total-label' => 'Total PASSIF',
                        'subsections' => [
                            'current-liabilities' => [
                                'title'       => 'Passifs circulants',
                                'total-label' => 'Total passifs circulants',
                            ],
                            'non-current-liabilities' => [
                                'title'       => 'Passifs non courants',
                                'total-label' => 'Total passifs non courants',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title'       => 'CAPITAUX PROPRES',
                        'total-label' => 'Total CAPITAUX PROPRES',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title'          => 'Résultats non affectés',
                                'current-year'   => 'Résultat non affecté de l\'exercice en cours',
                                'previous-years' => 'Résultats non affectés des exercices précédents',
                                'total-label'    => 'Total résultats non affectés',
                            ],
                            'retained-earnings' => [
                                'title'       => 'Report à nouveau',
                                'total-label' => 'Total report à nouveau',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'PASSIF + CAPITAUX PROPRES',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'Compte de résultat',
                'group' => 'Rapports de synthèse',
            ],
            'actions' => [
                'export-excel' => 'Exporter vers Excel',
                'export-pdf'   => 'Exporter vers PDF',
            ],
            'filters' => [
                'date-range' => 'Période',
                'journals'   => 'Journaux',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title'         => 'PRODUITS',
                        'total-label'   => 'Total des produits',
                        'empty-message' => 'Aucun compte de produits avec des transactions durant cette période',
                    ],
                    'expenses' => [
                        'title'         => 'CHARGES',
                        'total-label'   => 'Total des charges',
                        'empty-message' => 'Aucun compte de charges avec des transactions durant cette période',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'Grand livre',
                'group' => 'Rapports d\'audit',
            ],
            'actions' => [
                'export-excel' => 'Exporter vers Excel',
                'export-pdf'   => 'Exporter vers PDF',
            ],
            'filters' => [
                'date-range' => 'Période',
                'journals'   => 'Journaux',
            ],
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'Balance générale',
                'group' => 'Rapports d\'audit',
            ],
            'actions' => [
                'export-excel' => 'Exporter vers Excel',
                'export-pdf'   => 'Exporter vers PDF',
            ],
            'filters' => [
                'date-range' => 'Période',
                'journals'   => 'Journaux',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'Grand livre partenaires',
                'group' => 'Rapports partenaires',
            ],
            'actions' => [
                'export-excel' => 'Exporter Excel',
                'export-pdf'   => 'Exporter PDF',
            ],
            'filters' => [
                'date-range' => 'Période',
                'partners'   => 'Partenaires',
                'journals'   => 'Journaux',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'Balance âgée clients',
                'group' => 'Rapports partenaires',
            ],
            'actions' => [
                'export-excel' => 'Exporter Excel',
                'export-pdf'   => 'Exporter PDF',
            ],
            'filters' => [
                'as-of'         => 'À la date du',
                'based-on'      => 'Basé sur',
                'period-length' => 'Durée de la période (jours)',
                'journals'      => 'Journaux',
                'partners'      => 'Partenaires',
                'entries'       => 'Écritures',
                'options'       => [
                    'due-date'       => 'Date d\'échéance',
                    'invoice-date'   => 'Date de facture',
                    'days-30'        => '30 jours',
                    'days-60'        => '60 jours',
                    'days-90'        => '90 jours',
                    'posted-entries' => 'Écritures comptabilisées',
                    'all-entries'    => 'Toutes les écritures',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'Balance âgée fournisseurs',
                'group' => 'Rapports partenaires',
            ],
            'actions' => [
                'export-excel' => 'Exporter Excel',
                'export-pdf'   => 'Exporter PDF',
            ],
            'filters' => [
                'as-of'         => 'À la date du',
                'based-on'      => 'Basé sur',
                'period-length' => 'Durée de la période (jours)',
                'journals'      => 'Journaux',
                'partners'      => 'Partenaires',
                'entries'       => 'Écritures',
                'options'       => [
                    'due-date'       => 'Date d\'échéance',
                    'invoice-date'   => 'Date de facture',
                    'days-30'        => '30 jours',
                    'days-60'        => '60 jours',
                    'days-90'        => '90 jours',
                    'posted-entries' => 'Écritures comptabilisées',
                    'all-entries'    => 'Toutes les écritures',
                ],
            ],
        ],
    ],
];
