<?php

return [
    'title' => 'Gérer les opérations',

    'form' => [
        'enable-work-orders' => [
            'label'       => 'Ordres de travail',
            'helper-text' => 'Exécuter les opérations aux postes de travail désignés.',
            'link-text'   => 'Configurer les postes de travail',
        ],

        'enable-work-order-dependencies' => [
            'label'       => 'Dépendances des ordres de travail',
            'helper-text' => 'Définir l\'ordre dans lequel les ordres de travail doivent être traités. Activez cette fonctionnalité depuis l\'onglet Divers de chaque nomenclature.',
        ],

        'enable-byproducts' => [
            'label'       => 'Sous-produits',
            'helper-text' => 'Générer des sous-produits pendant la production (A + B → C + D).',
        ],
    ],
];
