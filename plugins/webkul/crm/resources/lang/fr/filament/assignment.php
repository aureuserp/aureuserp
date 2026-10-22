<?php

return [
    'rules' => [
        'title'         => 'Règles d\'attribution',
        'match'         => 'Correspondance',
        'match-all'     => 'Toutes les règles suivantes',
        'match-any'     => 'L\'une des règles suivantes',
        'conditions'    => 'Conditions',
        'field'         => 'Champ',
        'operator'      => 'Opérateur',
        'value'         => 'Valeur',
        'add-condition' => 'Ajouter une condition',
        'matching'      => '{1} :count enregistrement ouvert correspond à ces règles|[0,*] :count enregistrements ouverts correspondent à ces règles',
        'no-conditions' => 'Aucune condition : toutes les pistes sont acceptées.',

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

        'operators' => [
            'contains'     => 'contient',
            'not_contains' => 'ne contient pas',
            '='            => 'est égal à',
            '!='           => 'n\'est pas égal à',
            'starts_with'  => 'commence par',
            'set'          => 'est défini',
            'not_set'      => 'n\'est pas défini',
            '>'            => 'supérieur à',
            '>='           => 'supérieur ou égal à',
            '<'            => 'inférieur à',
            '<='           => 'inférieur ou égal à',
            'in'           => 'est parmi',
            'not_in'       => 'n\'est pas parmi',

            'date' => [
                '>='      => 'est le ou après le',
                '<='      => 'est le ou avant le',
                'set'     => 'est défini',
                'not_set' => 'n\'est pas défini',
            ],
        ],
    ],

    'team' => [
        'section'                => 'Règles d\'attribution',
        'section-description'    => 'Les pistes que l\'attribution basée sur des règles donne à cette équipe.',
        'capacity'               => ':assigned pistes attribuées ces 30 derniers jours sur un maximum de :max.',
        'use-leads'              => 'Pistes',
        'use-opportunities'      => 'Opportunités',
        'skip-assignment'        => 'Ignorer l\'assignation automatique',
        'skip-assignment-helper' => 'Les pistes ne sont pas assignées automatiquement à cette équipe.',

        'assign-leads' => [
            'label'        => 'Assigner les pistes',
            'description'  => 'Assigner maintenant les pistes sans vendeur de cette équipe à ses membres, selon les règles d\'assignation.',
            'notification' => ':count piste(s) assignée(s) aux vendeurs.',
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
            'capacity-assigned' => ':count attribuées ces 30 derniers jours.',
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
            'removed-from' => 'Retiré de : :teams (Équipes multiples désactivé).',

            'single-team' => [
                'title' => 'Un vendeur ne peut être que dans une seule équipe',
            ],
        ],
    ],
];
