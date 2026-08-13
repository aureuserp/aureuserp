<?php

return [

    'uninstall' => [
        'title'   => 'Confirmation de désinstallation',
        'message' => 'Êtes-vous sûr de vouloir désinstaller le plugin :name ?',
        'warning' => '⚠️ Cette action est irréversible et supprimera définitivement les données.',
    ],

    'dependents' => [
        'title'         => 'Plugins dépendants',
        'description'   => "Ces plugins dépendent de celui-ci. Tout plugin dépendant installé doit d'abord être désinstallé.",
        'installed'     => 'Installé',
        'not_installed' => 'Non installé',
    ],

    'dependency_warning' => [
        'title'   => 'Action requise',
        'message' => '⚠️ Veuillez d\'abord désinstaller les plugins dépendants suivants avant de désinstaller :name.',
    ],

    'data_impact' => [
        'title'       => 'Impact sur les données',
        'description' => 'Les tables de base de données suivantes contiennent des données qui seront définitivement supprimées.',
        'records'     => ':count enregistrements',
    ],

];
