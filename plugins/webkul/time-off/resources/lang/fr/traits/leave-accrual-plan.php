<?php

return [
    'form' => [
        'fields' => [
            'accrual-amount'              => 'Montant de l\'acquisition',
            'accrual-value-type'          => 'Type de valeur de l\'acquisition',
            'accrual-frequency'           => 'Fréquence de l\'acquisition',
            'accrual-day'                 => 'Jour de l\'acquisition',
            'day-of-month'                => 'Jour du mois',
            'first-day-of-month'          => 'Premier jour du mois',
            'second-day-of-month'         => 'Deuxième jour du mois',
            'first-period-month'          => 'Mois de la première période',
            'first-period-day'            => 'Jour de la première période',
            'second-period-month'         => 'Mois de la deuxième période',
            'second-period-day'           => 'Jour de la deuxième période',
            'first-period-year'           => 'Année de la première période',
            'cap-accrued-time'            => 'Plafonner le temps acquis',
            'days'                        => 'Jours',
            'start-count'                 => 'Compteur de début',
            'start-type'                  => 'Type de début',
            'action-with-unused-accruals' => 'Action avec les acquisitions non utilisées',
            'milestone-cap'               => 'Plafond de jalon',
            'maximum-leave-yearly'        => 'Congé maximum annuel',
            'accrual-validity'            => 'Validité de l\'acquisition',
            'accrual-validity-count'      => 'Compteur de validité de l\'acquisition',
            'accrual-validity-type'       => 'Type de validité de l\'acquisition',
            'advanced-accrual-settings'   => 'Paramètres avancés d\'acquisition',
            'after-allocation-start'      => 'Après la date de début de l\'allocation',
            'to-date'                     => 'Jusqu\'à la date',
        ],
    ],

    'table' => [
        'columns' => [
            'accrual-amount'     => 'Montant de l\'acquisition',
            'accrual-value-type' => 'Type de valeur de l\'acquisition',
            'frequency'          => 'Fréquence',
            'maximum-leave-days' => 'Jours de congé maximum',
        ],

        'groups' => [
            'accrual-amount'       => 'Montant de l\'acquisition',
            'accrual-value-type'   => 'Type de valeur de l\'acquisition',
            'frequency'            => 'Fréquence',
            'maximum-leave-days'   => 'Jours de congé maximum',
        ],

        'filters' => [
            'accrual-frequency'           => 'Fréquence de l\'acquisition',
            'start-type'                  => 'Type de début',
            'cap-accrued-time'            => 'Plafonner le temps acquis',
            'action-with-unused-accruals' => 'Action avec les acquisitions non utilisées',
            'accrual-amount'              => 'Montant de l\'acquisition',
            'accrual-frequency'           => 'Fréquence de l\'acquisition',
            'start-type'                  => 'Type de début',
            'created-at'                  => 'Créé le',
            'updated-at'                  => 'Mis à jour le',
        ],

        'header-actions' => [
            'created' => [
                'title' => 'Nouveau plan d\'acquisition de congé',

                'notification' => [
                    'title' => 'Plan d\'acquisition de congé créé',
                    'body'  => 'Le plan d\'acquisition de congé a été créé avec succès.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Plan d\'acquisition de congé mis à jour',
                    'body'  => 'Le plan d\'acquisition de congé a été mis à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plan d\'acquisition de congé supprimé',
                    'body'  => 'Le plan d\'acquisition de congé a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [

            'delete' => [
                'notification' => [
                    'title' => 'Plans d\'acquisition de congé supprimés',
                    'body'  => 'Les plans d\'acquisition de congé ont été supprimés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'accrual-amount'              => 'Montant de l\'acquisition',
            'accrual-value-type'          => 'Type de valeur de l\'acquisition',
            'accrual-frequency'           => 'Fréquence de l\'acquisition',
            'accrual-day'                 => 'Jour de l\'acquisition',
            'day-of-month'                => 'Jour du mois',
            'first-day-of-month'          => 'Premier jour du mois',
            'second-day-of-month'         => 'Deuxième jour du mois',
            'first-period-month'          => 'Mois de la première période',
            'first-period-day'            => 'Jour de la première période',
            'second-period-month'         => 'Mois de la deuxième période',
            'second-period-day'           => 'Jour de la deuxième période',
            'first-period-year'           => 'Année de la première période',
            'cap-accrued-time'            => 'Plafonner le temps acquis',
            'days'                        => 'Jours',
            'start-count'                 => 'Compteur de début',
            'start-type'                  => 'Type de début',
            'action-with-unused-accruals' => 'Action avec les acquisitions non utilisées',
            'milestone-cap'               => 'Plafond de jalon',
            'maximum-leave-yearly'        => 'Congé maximum annuel',
            'accrual-validity'            => 'Validité de l\'acquisition',
            'accrual-validity-count'      => 'Compteur de validité de l\'acquisition',
            'accrual-validity-type'       => 'Type de validité de l\'acquisition',
            'advanced-accrual-settings'   => 'Paramètres avancés d\'acquisition',
            'after-allocation-start'      => 'Après la date de début de l\'allocation',
        ],
    ],
];
