<?php

return [
    'model-label'        => 'Règle de visiteurs en pistes',
    'plural-model-label' => 'Règles de visiteurs en pistes',

    'navigation' => [
        'title' => 'Règles de visiteurs en pistes',
        'group' => 'Génération de pistes',
    ],

    'form' => [
        'name'                     => 'Nom de la règle',
        'name-placeholder'         => 'ex. Visiteurs des États-Unis',
        'active'                   => 'Active',
        'countries'                => 'Pays',
        'countries-helper'         => 'Déterminé à partir de l\'adresse IP du visiteur. Vide = tous les pays.',
        'states'                   => 'États',
        'states-helper'            => 'Connus uniquement pour les clients connectés (depuis leur adresse). Vide = tous les états.',
        'url'                      => 'Expression d\'URL',
        'url-placeholder'          => 'ex. /page',
        'url-helper'               => 'Pages prises en compte, sous forme d\'expression régulière sur le chemin. Vide = tout le site, "/" = page d\'accueil uniquement, "/pricing|/shop" = pages contenant /pricing ou /shop.',
        'url-invalid'              => 'Saisissez une expression valide.',
        'type'                     => 'Type',
        'suffix'                   => 'Suffixe',
        'suffix-helper'            => 'Ajouté au nom de la piste, ex. "Visiteur du site #12 (Inde) - Tarifs".',
        'team'                     => 'Équipe commerciale',
        'salesperson'              => 'Vendeur',
        'tags'                     => 'Étiquettes',
        'priority'                 => 'Priorité',
        'company'                  => 'Société',
        'company-helper'           => 'Vide = la société du client ou la première société active.',
        'lead-for'                 => 'Suivi des données',
        'industries'               => 'Secteurs',
        'industries-helper'        => 'Saisissez un nom pour ajouter un secteur.',
        'filter-on-size'           => 'Filtrer sur la taille',
        'size-min'                 => 'Taille de société à partir de',
        'size-max'                 => 'Taille de société jusqu\'à',
        'employees'                => 'employés',
        'gdpr'                     => 'Vérifiez si vous devez respecter le RGPD pour conserver des données personnelles.',
        'contact-filter'           => 'Filtrer les contacts sur',
        'extra-contacts'           => 'Nombre de contacts',
        'preferred-role'           => 'Rôle préféré',
        'other-roles'              => 'Autres rôles',
        'seniority'                => 'Ancienneté',

        'sections' => [
            'traffic'          => 'Conditions de trafic du site',
            'generation'       => 'Conditions de génération d\'opportunités',
            'lead-generation'  => 'Conditions de génération de pistes',
            'contact-filter'   => 'Filtre des contacts',
            'lead-data'        => 'Données de la piste',
            'opportunity-data' => 'Données de l\'opportunité',
            'settings'         => 'Paramètres',
        ],
    ],

    'table' => [
        'name'          => 'Nom de la règle',
        'url'           => 'Expression d\'URL',
        'whole-website' => 'Tout le site',
        'countries'     => 'Pays',
        'all-countries' => 'Tous les pays',
        'type'          => 'Type',
        'team'          => 'Équipe commerciale',
        'salesperson'   => 'Vendeur',
        'leads'         => 'Pistes',
        'opportunities' => 'Opportunités',
        'active'        => 'Active',
    ],

    'pages' => [
        'leads' => [
            'navigation' => 'Pistes générées',
            'title'      => 'Pistes générées : :rule',

            'columns' => [
                'name'       => 'Nom',
                'country'    => 'Pays',
                'stage'      => 'Étape',
                'created-at' => 'Créé le',
            ],
        ],
    ],
];
