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
