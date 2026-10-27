<?php

return [
    'rules' => [
        'title'         => 'Règles d\'attribution',
        'conditions'    => 'Conditions',
        'field'         => 'Champ',
        'operator'      => 'Opérateur',
        'value'         => 'Valeur',
        'add-condition' => 'Nouvelle règle',

        'heading-all' => 'Correspond à toutes les règles suivantes :',
        'heading-any' => 'Correspond à l\'une des règles suivantes :',
        'records'     => '{0} Aucun enregistrement correspondant|{1} :count enregistrement|[2,*] :count enregistrements',
        'breakdown'   => ':leads pistes · :opportunities opportunités',

        'records-modal' => [
            'heading'     => 'Enregistrements correspondants',
            'description' => 'Pistes et opportunités ouvertes pas encore attribuées et correspondant à ces règles. Celles déjà dans une équipe (ou, pour un membre, déjà avec un vendeur) sont exclues.',
            'empty'       => 'Aucune piste ou opportunité ouverte ne correspond à ces règles.',
            'showing'     => 'Affichage des :shown plus récents sur :total enregistrements.',
            'close'       => 'Fermer',

            'columns' => [
                'name'             => 'Nom',
                'type'             => 'Type',
                'contact'          => 'Contact',
                'email'            => 'E-mail',
                'salesperson'      => 'Vendeur',
                'expected-revenue' => 'Revenu attendu',
                'stage'            => 'Étape',
            ],
        ],

        'fields' => [
            'type'                  => 'Type',
            'name'                  => 'Nom',
            'contact_name'          => 'Nom du contact',
            'company_name'          => 'Nom de l\'entreprise',
            'job_title'             => 'Poste',
            'email'                 => 'E-mail',
            'phone'                 => 'Téléphone',
            'website'               => 'Site web',
            'street1'               => 'Rue',
            'city'                  => 'Ville',
            'zip'                   => 'Code postal',
            'state_id'              => 'État',
            'country_id'            => 'Pays',
            'stage_id'              => 'Étape',
            'tag_ids'               => 'Étiquettes',
            'priority'              => 'Priorité',
            'probability'           => 'Probabilité (%)',
            'expected_revenue'      => 'Revenu attendu',
            'expected_closing_date' => 'Clôture prévue',
            'created_at'            => 'Créé le',
            'utm_campaign_id'       => 'Campagne',
            'utm_medium_id'         => 'Support',
            'utm_source_id'         => 'Source',
            'referred_by'           => 'Recommandé par',
        ],
    ],

    'team' => [
        'company-helper'         => 'Sociétés activées dans la barre du haut. Pour créer une équipe d\'une autre société, passez d\'abord à celle-ci.',
        'company-not-active'     => 'Cette société n\'est pas active. Passez-y d\'abord dans la barre du haut.',
        'section'                => 'Règles d\'attribution',
        'section-description'    => 'Les pistes que l\'attribution basée sur des règles donne à cette équipe.',
        'capacity'               => ':assigned pistes attribuées ce mois-ci sur un maximum de :max',
        'use-leads'              => 'Pistes',
        'use-opportunities'      => 'Opportunités',
        'skip-assignment'        => 'Ignorer l\'assignation automatique',
        'skip-assignment-helper' => 'Les pistes ne sont pas assignées automatiquement à cette équipe.',

        'assign-leads' => [
            'label'          => 'Assigner les pistes',
            'heading'        => 'Attribuer des pistes',
            'description'    => 'Choisissez les pistes à répartir. Chacune va au membre de l\'équipe qui a le plus de place libre.',
            'submit'         => 'Attribuer',
            'records'        => 'Pistes à attribuer',
            'empty'          => 'Sélectionnez au moins une piste.',
            'skipped'        => 'Impossible de répartir :count (pas de place libre, ou les règles du membre ne correspondent pas).',
            'capacity'       => 'Place libre : :members',
            'no-members'     => 'Aucun membre de l\'équipe ne peut recevoir de pistes pour le moment.',
            'nothing'        => 'Aucune piste à attribuer pour le moment.',
            'notification'   => ':count piste(s) assignée(s) aux vendeurs.',
        ],
    ],

    'members' => [
        'new-salesperson' => [
            'heading' => 'Nouveau vendeur',
        ],

        'title'       => 'Membres',
        'description' => 'Les vendeurs de cette équipe, le nombre de pistes que chacun peut recevoir en 30 jours et les pistes qu\'ils acceptent.',
        'empty'       => 'Aucun membre pour le moment',

        'fields' => [
            'salesperson'       => 'Vendeur',
            'capacity'          => 'Pistes max. (30 jours)',
            'capacity-helper'   => 'Nombre maximal de pistes assignées à ce vendeur sur 30 jours.',
            'paused'            => 'Suspendre l\'assignation',
            'paused-helper'     => 'Ce vendeur ne reçoit plus de nouvelles pistes automatiquement.',
            'active'            => 'Actif',
        ],

        'columns' => [
            'salesperson' => 'Vendeur',
            'capacity'    => 'Pistes max. (30 jours)',
            'last-month'  => 'Assignées (30 jours)',
            'rules'       => 'Règles',
            'paused'      => 'Suspendu',
            'active'      => 'Actif',
        ],

        'actions' => [
            'add'    => 'Ajouter un membre',
            'remove' => 'Retirer',
        ],

        'notifications' => [
            'added'        => 'Membre ajouté',
            'removed-from' => 'Retiré de : :teams (Commerciaux dans plusieurs équipes désactivé).',

            'single-team' => [
                'title' => 'Un vendeur ne peut être que dans une seule équipe',
            ],
        ],
    ],
];
