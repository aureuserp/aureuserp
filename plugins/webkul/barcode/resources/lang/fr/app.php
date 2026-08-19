<?php

return [
    'title' => 'Code-barres',

    'navigation' => [
        'back'        => 'Retour',
        'home'        => 'Opérations',
        'search'      => 'Rechercher...',
        'label'       => 'Navigation',
        'open'        => 'Ouvrir la navigation',
        'coming-soon' => 'Bientôt disponible',
    ],

    'auth' => [
        'login-title'       => 'Connexion Code-barres',
        'login-heading'     => 'Connexion à Code-barres',
        'login-subheading'  => "Continuer vers l'application des opérations de codes-barres.",
    ],

    'filament' => [
        'navigation' => [
            'group' => 'Code-barres',
            'label' => 'Application Code-barres',
        ],
    ],

    'dashboard' => [
        'operations' => 'Opérations',
        'empty'      => 'Aucune opération disponible.',
    ],

    'operation-search' => [
        'placeholder'    => "Scanner ou saisir le code-barres de l'opération...",
        'open'           => 'Ouvrir',
        'not-found'      => 'Aucune opération active trouvée pour ce code-barres.',
        'multiple-found' => ':count opérations correspondantes trouvées.',
    ],

    'transfers' => [
        'title' => 'Transferts',
        'empty' => 'Aucun transfert trouvé.',
    ],

    'adjustments' => [
        'title'             => 'Ajustements de stock',
        'subtitle'          => 'Compter le stock par emplacement, produit ou lot',
        'search'            => 'Scanner ou rechercher par emplacement, produit, lot, série...',
        'empty'             => 'Aucune quantité de stock trouvée.',
        'location-scanned'  => "Emplacement scanné : :location. Scannez d'autres produits ici ou scannez un autre emplacement.",
        'location-cleared'  => "Filtres d'ajustement de stock effacés.",
        'product-not-found' => "Ce produit n'est pas disponible dans la sélection de stock actuelle.",
        'lot-not-found'     => "Ce lot ou ce numéro de série n'est pas disponible dans la sélection de stock actuelle.",
        'multiple-found'    => ':count quantités de stock correspondantes trouvées.',
        'count-saved'       => 'Comptage de stock enregistré.',
        'count-applied'     => "Ajustement de stock appliqué.",
        'count-cleared'     => 'Comptage de stock effacé.',
        'counted'           => 'Compté',
        'on-hand'           => 'En stock',
        'location'          => 'Emplacement',
        'product'           => 'Produit',
        'lot-serial'        => 'Lot/Série',
        'clear-filters'     => 'Effacer les filtres',
        'apply'             => 'Appliquer',
        'clear'             => 'Effacer',
        'editor-title'      => "Détails de l'ajustement",
        'editor-subtitle'   => 'Vérifiez les détails du stock et mettez à jour la quantité comptée.',
        'editor-image'      => 'Image de la quantité de stock',
        'edit-tooltip'      => 'Modifier la quantité de stock',
    ],

    'operation' => [
        'scan'                 => 'Scanner un produit, lot, colis, emballage ou transfert',
        'manual-scan'          => 'Scanner ou rechercher par produit, référence, code-barres...',
        'search'               => 'Rechercher produit, référence, code-barres...',
        'moves'                => 'Mouvements',
        'source'               => 'Depuis',
        'available'            => 'Disponible',
        'discard'              => 'Ignorer',
        'confirm'              => 'Confirmer',
        'counted'              => 'Compté',
        'lot-serial'           => 'Numéro de lot/série',
        'stock-title'          => 'Quantité en stock',
        'empty-moves'          => 'Aucun mouvement trouvé.',
        'details-title'        => 'Détails du mouvement',
        'settings-title'       => 'Paramètres du mouvement',
        'pick-from'            => 'Prélever depuis',
        'destination-location' => 'Emplacement de destination',
        'destination-package'  => 'Colis de destination',
        'select-package'       => 'Sélectionner un colis',
        'stock-subtitle'       => "Sélectionnez un autre endroit où prélever le produit",
        'no-stock-locations'   => 'Aucun emplacement de stock trouvé.',
        'camera-unavailable'   => 'Caméra indisponible',
        'submit-scan'          => 'Valider le scan',
        'image-alt'            => 'Image de la ligne de mouvement',
        'edit-tooltip'         => 'Modifier la ligne de mouvement',
    ],

    'scan' => [
        'empty'                    => 'Saisissez ou scannez un code-barres.',
        'not-found'                => 'Aucun code-barres correspondant trouvé.',
        'operation-matched'        => 'Transfert trouvé.',
        'product-not-on-operation' => "Ce produit ne fait pas partie de l'opération.",
        'package-matched'          => 'Colis trouvé.',
        'move-located'             => 'Mouvement localisé. Saisissez la quantité comptée.',
        'move-updated'             => 'Quantité du mouvement mise à jour.',
        'move-counted'             => 'Mouvement marqué comme compté.',
    ],

    'actions' => [
        'confirm'                  => 'Confirmer',
        'confirm-prompt'           => 'Êtes-vous sûr de vouloir',
        'cancel'                   => 'Annuler',
        'check-availability'       => 'Vérifier la disponibilité',
        'validate'                 => 'Valider',
        'return'                   => 'Retourner',
        'stay-on-transfer'         => 'Ignorer',
        'no-backorder'             => 'Pas de reliquat',
        'backorder-title'          => 'Transfert incomplet',
        'backorder-prompt'         => 'Si vous validez maintenant, les produits restants seront ajoutés à un reliquat.',
        'backorder-col-product'    => 'Produit',
        'backorder-col-done-todo'  => 'Fait / À faire',
        'backorder-col-backorder'  => 'Reliquat',
        'completed'                => 'Action terminée.',
        'unsupported'              => 'Action de code-barres non prise en charge.',
        'no-moves'                 => "Cette opération n'a aucun mouvement.",
        'no-return-quantities'     => 'Il n\'y a aucune quantité à retourner.',
    ],
];
