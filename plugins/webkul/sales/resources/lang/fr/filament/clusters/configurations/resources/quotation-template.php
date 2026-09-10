<?php

return [
    'title' => 'Modèle de devis',

    'navigation' => [
        'title'  => 'Modèle de devis',
        'group'  => 'Commandes clients',
    ],

    'form' => [
        'tabs' => [
            'products' => [
                'title'  => 'Produits',
                'fields' => [
                    'products'     => 'Produits',
                    'name'         => 'Nom',
                    'quantity'     => 'Quantité',
                ],
            ],

            'terms-and-conditions' => [
                'title'  => 'Conditions générales',
                'fields' => [
                    'note-placeholder' => 'Rédigez vos conditions générales pour les devis.',
                ],
            ],
        ],

        'sections' => [
            'general' => [
                'title' => 'Informations générales',

                'fields' => [
                    'name'               => 'Nom',
                    'quotation-validity' => 'Validité du devis',
                    'sale-journal'       => 'Journal de vente',
                ],
            ],

            'signature-and-payment' => [
                'title' => 'Signature et paiements',

                'fields' => [
                    'online-signature'      => 'Signature en ligne',
                    'online-payment'        => 'Paiement en ligne',
                    'prepayment-percentage' => 'Pourcentage d\'acompte',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'created-by'            => 'Créé par',
            'company'               => 'Société',
            'name'                  => 'Nom',
            'number-of-days'        => 'Nombre de jours',
            'journal'               => 'Journal de vente',
            'signature-required'    => 'Signature requise',
            'payment-required'      => 'Paiement requis',
            'prepayment-percentage' => 'Pourcentage d\'acompte',
        ],
        'groups'  => [
            'company' => 'Société',
            'name'    => 'Nom',
            'journal' => 'Journal',
        ],
        'filters' => [
            'created-by' => 'Créé par',
            'company'    => 'Société',
            'name'       => 'Nom',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Modèle de devis supprimé',
                    'body'  => 'Le modèle de devis a été supprimé avec succès.',
                ],
            ],

        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Modèle de devis supprimé',
                    'body'  => 'Le modèle de devis a été supprimé avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'products' => [
                'title' => 'Produits',
            ],
            'terms-and-conditions' => [
                'title' => 'Conditions générales',
            ],
        ],
        'sections' => [
            'general' => [
                'title' => 'Informations générales',
            ],
            'signature_and_payment' => [
                'title' => 'Signature et paiement',
            ],
        ],
        'entries' => [
            'product'               => 'Produit',
            'description'           => 'Description',
            'quantity'              => 'Quantité',
            'unit-price'            => 'Prix unitaire',
            'section-name'          => 'Nom de la section',
            'note-title'            => 'Titre de la note',
            'name'                  => 'Nom du modèle',
            'quotation-validity'    => 'Validité du devis',
            'sale-journal'          => 'Journal de vente',
            'online-signature'      => 'Signature en ligne',
            'online-payment'        => 'Paiement en ligne',
            'prepayment-percentage' => 'Pourcentage d\'acompte',
        ],
    ],
];
