<?php

return [
    'model-label' => 'Séquence',

    'plural-model-label' => 'Séquences',

    'navigation' => [
        'title' => 'Séquences',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',
                'description' => 'Les séquences pour les journaux, entrepôts et types d\'opération sont créées automatiquement lors de la création de ces enregistrements — modifiez-les ici. La création manuelle n\'est nécessaire que pour les séquences personnalisées basées sur un code.',

                'fields' => [
                    'name'      => 'Nom',
                    'code'      => 'Code',
                    'code-help' => 'Identifiant technique utilisé par les documents, par exemple sales.order. Les séquences créées automatiquement possèdent déjà le bon code.',
                    'company'   => 'Société',
                ],
            ],

            'format' => [
                'title' => 'Numérotation',

                'fields' => [
                    'prefix'          => 'Préfixe',
                    'prefix-help'     => 'Espaces réservés : %(year), %(y), %(month), %(day). Exemple : INV/%(year)/',
                    'suffix'          => 'Suffixe',
                    'padding'         => 'Remplissage des chiffres',
                    'next-number'     => 'Numéro suivant',
                    'next-number-help' => 'Ne peut être qu\'augmenté. Pour redémarrer la numérotation en toute sécurité, supprimez la séquence — elle sera recréée à partir du numéro de document existant le plus élevé.',
                    'step'            => 'Pas',
                    'reset-frequency' => 'Réinitialisation du compteur',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Nom',
            'code'            => 'Code',
            'applies-to'      => 'Applicable à',
            'company'         => 'Société',
            'next-preview'    => 'Prochain numéro de document',
            'next-number'     => 'Numéro suivant',
            'reset-frequency' => 'Réinitialisation du compteur',
        ],

        'variants' => [
            'refund'         => 'Remboursement',
            'payment'        => 'Paiement',
            'refund-payment' => 'Remboursement + paiement',
        ],

        'filters' => [
            'company' => 'Société',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Séquence mise à jour',
                    'body'  => 'La séquence a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Séquence supprimée',
                    'body'  => 'La séquence a été supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Séquences supprimées',
                    'body'  => 'Les séquences ont été supprimées avec succès.',
                ],
            ],
        ],
    ],
];
