<?php

return [
    'manufacturing-manager' => [
        'unplan-order' => [
            'work-orders-already-done'    => "Certains ordres de travail sont déjà terminés, vous ne pouvez donc pas déplanifier cet ordre de fabrication.\n\nCe serait dommage de gâcher tout ce progrès, n'est-ce pas ?",
            'work-orders-already-started' => "Certains ordres de travail ont déjà démarré, vous ne pouvez donc pas déplanifier cet ordre de fabrication.\n\nCe serait dommage de gâcher tout ce progrès, n'est-ce pas ?",
        ],
    ],

    'work-center-productivity-log' => [
        'time-tracking'                    => 'Suivi du temps : :name',
        'no-performance-productivity-loss' => "Vous devez définir au moins une perte de productivité non archivée dans la catégorie 'Performance'. Créez-la depuis les paramètres de configuration.",
    ],

    'work-center' => [
        'already-unblocked' => 'Il a déjà été débloqué.',
    ],

    'work-order' => [
        'unblock-work-center'        => 'Veuillez débloquer le poste de travail pour démarrer l\'ordre de travail.',
        'already-done-or-cancelled'  => 'Vous ne pouvez pas démarrer un ordre de travail déjà terminé ou annulé',
        'no-calendar-on-work-center' => 'Aucun calendrier n\'est défini sur le poste de travail :name.',
        'no-productivity-loss'       => "Vous devez définir au moins une perte de productivité dans la catégorie 'Productivité'. Créez les paramètres de configuration.",
        'no-performance-loss'        => "Vous devez définir au moins une perte de productivité dans la catégorie 'Performance'. Créez les paramètres de configuration.",
        'impossible-to-plan'         => 'Impossible de planifier l\'ordre de travail. Veuillez vérifier les disponibilités du poste de travail.',
    ],

    'order' => [
        'product-in-byproducts'                    => 'Vous ne pouvez pas avoir :product comme produit fini et dans les sous-produits',
        'missing-lot-serial-number'                => 'Vous devez fournir un numéro de lot/série pour les produits et les "consommer" : :missing_products',
        'serial-number-already-produced'           => 'Ce numéro de série pour le produit :product a déjà été produit',
        'byproduct-serial-number-already-produced' => 'Le numéro de série :number utilisé pour le sous-produit :product a déjà été produit',
        'component-serial-number-consumed'         => 'Le numéro de série :number utilisé pour le composant :component a déjà été consommé',
        'components-availability'                  => [
            'available'     => 'Disponible',
            'not-available' => 'Non disponible',
            'expected'      => 'Prévu :date',
        ],
    ],
];
