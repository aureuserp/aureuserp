<?php

return [
    'inventory-manager' => [
        'check-availability' => [
            'no-moves' => 'Rien à vérifier pour la disponibilité.',
        ],

        'cancel-move' => [
            'already-done' => 'Vous ne pouvez pas annuler un mouvement de stock défini sur \'Terminé\'. Créez un retour afin d\'inverser les mouvements effectués.',
        ],

        'unreserve-move' => [
            'already-done' => "Vous ne pouvez pas annuler la réservation d'un mouvement de stock défini sur 'Terminé'.",
        ],

        'validate' => [
            'quantity-rounding-mismatch' => 'La quantité effectuée pour le produit ":product" ne respecte pas la précision d\'arrondi définie sur l\'unité de mesure ":unit". Veuillez modifier la quantité effectuée ou la précision d\'arrondi de votre unité de mesure.',
            'no-negative-quantities'     => 'Les quantités négatives ne sont pas autorisées',
            'missing-lot-serial-number'  => "Vous devez fournir un numéro de lot/série pour le produit :\n:products",
        ],

        'run-procurement' => [
            'no-rule-found'      => "Aucune règle n'a été trouvée pour réapprovisionner \":product\" dans \":location\".\nVérifiez la configuration des routes sur le produit.",
            'no-source-location' => 'Aucun emplacement source défini sur la règle de stock : :name !',
            'no-vendor-price'    => 'Il n\'existe aucun prix fournisseur correspondant permettant de générer le bon de commande pour le produit :product (aucun fournisseur défini, quantité minimale non atteinte, dates non valides, ...). Rendez-vous sur la fiche produit et complétez la liste des fournisseurs.',
        ],

        'return' => [
            'origin' => 'Retour de :operation_name',
        ],
    ],

    'move-line' => [
        'negative-quantity-not-allowed' => 'La réservation d\'une quantité négative n\'est pas autorisée.',
    ],

    'product-quantity' => [
        'quantity-not-set'                 => 'La quantité ou la quantité réservée doit être définie.',
        'removal-strategy-not-implemented' => 'La stratégie de retrait :strategy n\'est pas implémentée.',
        'unreserve-more-than-stock'        => 'Il n\'est pas possible d\'annuler la réservation de plus de produits :name que vous n\'en avez en stock.',
    ],

    'product' => [
        'endless-loop-rule' => "Configuration de règle invalide, la règle suivante provoque une boucle infinie : :name",
    ],

    'move' => [
        'quantity-rounding-mismatch' => 'La quantité effectuée pour le produit :product ne respecte pas la précision d\'arrondi définie sur l\'unité de mesure :unit. Veuillez modifier la quantité effectuée ou la précision d\'arrondi de votre unité de mesure.',
        'split-done-or-cancel'       => 'Vous ne pouvez pas diviser un mouvement de stock défini sur \'Terminé\' ou \'Annulé\'.',
        'split-draft'                => 'Vous ne pouvez pas diviser un mouvement à l\'état brouillon. Il doit d\'abord être confirmé.',
        'serial-already-assigned'    => 'Le numéro de série a déjà été attribué au produit : :product, Numéro de série : :serial_number',

        'cross-company' => [
            'title' => 'Transfert inter-société non autorisé',
            'body'  => 'Un transfert ne peut pas déplacer directement du stock entre des emplacements appartenant à des sociétés différentes (:source et :destination). Les transferts inter-sociétés ne sont pas encore pris en charge.',
        ],
    ],

    'rule' => [
        'delay-on'     => 'Délai sur :name',
        'days'         => '+ :days jour(s)',
        'time-horizon' => 'Horizon temporel',
    ],
];
