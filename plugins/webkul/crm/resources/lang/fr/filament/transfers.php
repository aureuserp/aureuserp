<?php

return [
    'actions' => [
        'export' => [
            'label' => 'Exporter',
        ],

        'import' => [
            'label' => 'Importer',
        ],
    ],

    'columns' => [
        'id'                 => 'ID',
        'name'               => 'Nom',
        'contact-name'       => 'Nom du contact',
        'company-name'       => 'Nom de la société',
        'job-title'          => 'Poste',
        'email'              => 'E-mail',
        'phone'              => 'Téléphone',
        'website'            => 'Site web',
        'street1'            => 'Rue',
        'street2'            => 'Rue 2',
        'city'               => 'Ville',
        'zip'                => 'Code postal',
        'country'            => 'Pays',
        'stage'              => 'Étape',
        'salesperson'        => 'Vendeur',
        'team'               => 'Équipe commerciale',
        'priority'           => 'Priorité',
        'probability'        => 'Probabilité',
        'tags'               => 'Étiquettes',
        'source'             => 'Source',
        'medium'             => 'Support',
        'campaign'           => 'Campagne',
        'notes'              => 'Notes',
        'lost-reason'        => 'Motif de perte',
        'created-at'         => 'Créé le',
        'customer'           => 'Client',
        'expected-revenue'   => 'Revenu attendu',
        'prorated-revenue'   => 'Revenu au prorata',
        'expected-closing'   => 'Clôture prévue',
        'number'             => 'Numéro',
        'order-date'         => 'Date de commande',
        'expiration'         => 'Expiration',
        'delivery-date'      => 'Date de livraison',
        'customer-reference' => 'Référence client',
        'source-document'    => 'Document d\'origine',
        'untaxed-amount'     => 'Montant HT',
        'taxes'              => 'Taxes',
        'total'              => 'Total',
        'currency'           => 'Devise',
        'status'             => 'Statut',
        'invoice-status'     => 'Statut de facturation',
    ],

    'notifications' => [
        'export' => [
            'completed' => 'Votre export est prêt : :count lignes exportées.',
            'failed'    => ':count lignes n\'ont pas pu être exportées.',
        ],

        'import' => [
            'completed' => 'Votre import est terminé : :count lignes importées.',
            'failed'    => ':count lignes ont échoué ; téléchargez-les depuis cette notification pour voir pourquoi.',
        ],
    ],

    'errors' => [
        'record-not-found' => 'Aucun enregistrement avec l\'ID :id dans vos sociétés.',
        'cannot-update'    => 'Vous n\'êtes pas autorisé à modifier l\'enregistrement :id.',
        'cannot-create'    => 'Vous n\'êtes pas autorisé à créer des enregistrements.',
        'not-found'        => '« :value » introuvable pour :field.',
    ],
];
