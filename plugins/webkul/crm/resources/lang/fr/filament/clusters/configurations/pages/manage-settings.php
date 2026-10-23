<?php

return [
    'navigation' => [
        'title' => 'Paramètres',
    ],

    'title' => 'Paramètres',

    'actions' => [
        'assign-now' => [
            'label'        => 'Assigner maintenant',
            'description'  => 'Assigner maintenant les pistes non assignées aux équipes commerciales et aux vendeurs, selon les règles d\'assignation.',
            'notification' => ':teams piste(s) assignée(s) aux équipes et :salespeople aux vendeurs.',
        ],
    ],

    'form' => [
        'scoring' => [
            'section'            => 'Notation prédictive des pistes',
            'description'        => 'La probabilité de gagner chaque piste et opportunité ouverte est calculée à partir de l\'historique des gains et des pertes. L\'étape et l\'équipe commerciale sont toujours utilisées.',
            'variables'          => 'Prendre aussi en compte',
            'variables-helper'   => 'Les informations d\'un enregistrement prises en compte par la probabilité.',
            'start-date'         => 'Prendre en compte les pistes créées à partir du',
            'start-date-helper'  => 'Laissez vide pour utiliser tout l\'historique.',
            'update'             => 'Mettre à jour les probabilités',
            'update-description' => 'Enregistre ces choix, réapprend de l\'historique des gains et des pertes et recalcule tous les enregistrements ouverts.',
            'updated'            => 'Probabilités mises à jour pour :count enregistrement(s) ouvert(s).',
        ],

        'sections' => [
            'crm' => 'CRM',
        ],

        'use-leads' => [

            'label'  => 'Pistes',

            'helper' => 'Ajouter une étape de qualification avant la création d\'une opportunité.',

        ],

        'recurring-revenues' => [

            'label'  => 'Revenus récurrents',

            'helper' => 'Définir des plans et des revenus récurrents sur les opportunités.',

        ],

        'multi-teams' => [
            'label'  => 'Équipes multiples',
            'helper' => 'Affecter les vendeurs à plusieurs équipes commerciales.',
        ],

        'auto-assignment' => [
            'label'        => 'Assignation basée sur des règles',
            'helper'       => 'Assigner périodiquement les pistes selon des règles. Toutes les équipes commerciales utilisent ce paramètre par défaut, sauf indication contraire.',
            'running'      => 'Exécution',
            'manual'       => 'Manuellement',
            'auto'         => 'De façon répétée',
            'repeat-every' => 'Répéter toutes les',
            'unit'         => 'Unité',
            'next-run'     => 'Prochaine exécution : :date',

            'units' => [
                'minutes' => 'Minutes',
                'hours'   => 'Heures',
                'days'    => 'Jours',
                'weeks'   => 'Semaines',
            ],
        ],
    ],
];
