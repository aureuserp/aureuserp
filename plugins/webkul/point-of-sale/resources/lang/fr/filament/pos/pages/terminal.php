<?php

return [
    'common' => [
        'ok'      => 'Ok',
        'close'   => 'Fermer',
        'cancel'  => 'Annuler',
        'save'    => 'Enregistrer',
        'saving'  => 'Enregistrement…',
        'discard' => 'Abandonner',
        'clear'   => 'Effacer',
        'apply'   => 'Appliquer',
        'open'    => 'Ouvrir',
        'resume'  => 'Reprendre',
        'confirm' => 'Confirmer',
        'back'    => 'Retour',
        'print'   => 'Imprimer',
        'new'     => 'Nouveau',
        'more'    => '+ :count de plus',
    ],

    'nav' => [
        'home'      => 'Accueil',
        'orders'    => 'Commandes',
        'registers' => 'Caisses',
    ],

    'picker' => [
        'title'       => 'Caisses',
        'open'        => 'Ouvrir la caisse',
        'resume'      => 'Reprendre',
        'discard'     => 'Abandonner la session',
        'open-by'     => 'Ouvert par :name',
        'closed'      => 'Fermée',
        'opening'     => 'Ouverture…',
        'back-office' => 'Back-office',
        'logout'      => 'Se déconnecter',
        'offline'     => 'Connectez-vous au réseau pour ouvrir une caisse.',
        'failed'      => 'Impossible de joindre le serveur. Réessayez.',

        'discard-confirm' => [
            'heading' => 'Abandonner cette session ?',
            'confirm' => 'Abandonner',
        ],

        'empty' => [
            'heading'     => 'Aucun point de vente disponible',
            'description' => 'Créez un point de vente actif dans le back-office pour commencer à vendre.',
        ],
    ],

    'history' => [
        'heading'       => 'Commandes',
        'back'          => 'Retour',
        'search'        => 'Rechercher par commande, reçu, client ou table',
        'empty'         => 'Aucune commande ne correspond à cette recherche.',
        'select-order'  => 'Sélectionnez une commande pour voir ses détails.',
        'walk-in'       => 'Client de passage',
        'invoice'       => 'Facture',
        'invoiced'      => 'Facturée',
        'print-invoice' => 'Imprimer la facture',
        'cancel'        => 'Annuler la commande',
        'print'         => 'Imprimer le reçu',
        'details'       => 'Détails',
        'refund'        => 'Rembourser',
        'refund-prompt' => 'Sélectionnez le ou les produits à rembourser et définissez la quantité.',
        'to-refund'     => 'À rembourser',
        'refundable'    => 'Remboursable : :qty',
        'taxes'         => 'Taxes',
        'total'         => 'Total',
        'paid'          => 'Payé',
        'lines'         => 'Lignes',
        'payments'      => 'Paiements',
        'prev'          => 'Précédent',
        'next'          => 'Suivant',
        'page'          => 'Page :page sur :total',
        'unreachable'   => "Le serveur n'a pas pu être joint. Vérifiez la connexion et réessayez.",
        'failed'        => 'La requête a échoué (:status).',

        'invoice-customer' => [
            'heading'        => 'Un client est requis pour facturer',
            'question'       => 'Voulez-vous ouvrir la liste des clients pour en sélectionner un ?',
            'select-heading' => 'Sélectionnez un client à facturer',
        ],

        'columns' => [
            'date'     => 'Date',
            'receipt'  => 'Reçu',
            'order'    => 'Commande',
            'customer' => 'Client',
            'cashier'  => 'Caissier',
            'tracking' => 'Suivi',
            'table'    => 'Table',
            'total'    => 'Total',
            'status'   => 'Statut',
        ],

        'status' => [
            'active'   => 'Active',
            'draft'    => 'En cours',
            'paid'     => 'Payée',
            'done'     => 'Comptabilisée',
            'invoiced' => 'Facturée',
            'canceled' => 'Annulée',
        ],
    ],

    'parked' => [
        'walk-in'         => 'Client de passage',
        'items'           => '{1} :count article|[2,*] :count articles',
        'count'           => ':count en attente',
        'view-all'        => 'Tout voir',
        'heading'         => 'Commandes en attente',
        'search'          => 'Rechercher des commandes par nom, référence ou produit',
        'no-match'        => 'Aucune commande ne correspond à cette recherche.',
        'parked-ago'      => 'mise en attente :time',
        'discard'         => 'Abandonner la commande',
        'discard-confirm' => [
            'heading'     => 'Abandonner la commande en attente ?',
            'description' => 'Ses lignes seront perdues. Cette action est irréversible.',
        ],

        'empty' => [
            'heading'     => 'Rien en attente',
            'description' => 'Les commandes que vous mettez de côté vous attendront ici.',
        ],
    ],

    'opening-control' => [
        'heading'                  => "Contrôle d'ouverture",
        'cash'                     => "Espèces d'ouverture",
        'previous-closing-balance' => 'Solde de clôture de la session précédente : :amount',
        'note'                     => "Note d'ouverture",
        'placeholder'              => "Ajouter une note d'ouverture…",
        'confirm'                  => 'Ouvrir la caisse',
        'cancel'                   => 'Annuler',
    ],

    'tabs' => [
        'new'      => 'Nouvelle commande',
        'all'      => 'Toutes les commandes',
        'view-all' => 'Tout voir',
    ],

    'menu' => [
        'label'          => 'Menu',
        'orders'         => 'Commandes',
        'cash-in-out'    => "Entrée / Sortie d'espèces",
        'create-product' => 'Créer un produit',
        'back-office'    => 'Back-office',
        'close-register' => 'Fermer la caisse',
        'install'        => "Installer l'application",
        'theme'          => 'Changer de thème',
        'logout'         => 'Se déconnecter',
    ],

    'cash-movement' => [
        'heading' => "Entrée / sortie d'espèces",
        'in'      => "Entrée d'espèces",
        'out'     => "Sortie d'espèces",
        'amount'  => 'Montant',
        'reason'  => 'Motif',
        'confirm' => 'Enregistrer le mouvement',
        'close'   => 'Fermer',

        'notification' => [
            'title' => "Mouvement d'espèces enregistré",

            'body' => [
                'in'  => ':amount ajouté au tiroir-caisse.',
                'out' => ':amount retiré du tiroir-caisse.',
            ],
        ],
    ],

    'closing' => [
        'copy'                     => 'Copier le montant attendu',
        'heading'                  => 'Fermeture de la caisse',
        'expected'                 => 'Attendu dans le tiroir',
        'counted'                  => 'Compté',
        'note'                     => 'Note de clôture',
        'confirm'                  => 'Fermer la caisse',
        'back'                     => 'Retour à la caisse',
        'method'                   => 'Mode de paiement',
        'total'                    => 'Total',
        'opening'                  => 'Ouverture',
        'payments'                 => 'Paiements en espèces',
        'moves'                    => "Entrée / sortie d'espèces",
        'cash-in'                  => "Entrée d'espèces :number",
        'cash-out'                 => "Sortie d'espèces :number",
        'orders'                   => ':quantity commandes',
        'difference'               => 'Écart',
        'count'                    => 'Comptage des espèces',
        'clear'                    => 'Effacer',
        'discard'                  => 'Abandonner',
        'daily-sale'               => 'Vente du jour',
        'opening-note'             => "Note d'ouverture",
        'authorized-diff'          => 'Écart maximal autorisé : :amount',
        'authorized-diff-exceeded' => 'L’écart dépasse la limite autorisée. Seul un responsable peut fermer cette caisse.',
        'unbalanced'               => "L'écriture de clôture est déséquilibrée de :amount. Un responsable doit sélectionner un compte d'équilibrage pour clôturer.",
    ],

    'draft-orders' => [
        'heading'       => 'Erreur',
        'message'       => "Vous ne pouvez pas fermer le PdV tant que des commandes sont encore à l'état brouillon.",
        'review-orders' => 'Vérifier les commandes',
        'cancel-orders' => 'Annuler les commandes',
    ],

    'product-form' => [
        'heading'             => 'Nouveau produit',
        'name'                => 'Nom du produit',
        'name-placeholder'    => 'p. ex. Cheeseburger',
        'barcode'             => 'Code-barres',
        'barcode-placeholder' => 'p. ex. 1234567890',
        'tracking'            => "Suivre l'inventaire",
        'price'               => 'Prix de vente',
        'taxes'               => 'Taxes à la vente',
        'tax-included'        => '(= :amount TTC)',
        'category'            => 'Catégorie PdV',
        'unsaleable'          => 'Non vendable',

        'notification' => [
            'title' => 'Produit créé',
        ],

        'error' => [
            'failed'  => 'Impossible de créer le produit (:status)',
            'offline' => "La création d'un produit nécessite une connexion.",
        ],
    ],

    'product-info' => [
        'heading'          => 'Informations produit',
        'inventory'        => 'Inventaire',
        'available'        => 'disponibles sur cette caisse',
        'negative-warning' => 'La vente reste autorisée ; le stock passera en négatif et le back-office affichera le manque.',
        'financials'       => 'Données financières',
        'price'            => 'Prix',
        'cost'             => 'Coût',
        'margin'           => 'Marge',
        'order'            => 'Dans cette commande',
        'quantity'         => 'Quantité',
        'total-price'      => 'Prix total',
        'total-margin'     => 'Marge totale',
        'add'              => 'Ajouter à la commande',
    ],

    'customers' => [
        'heading'      => 'Sélectionner un client',
        'search'       => 'Rechercher des clients',
        'no-match'     => 'Aucun client ne correspond à cette recherche. Seuls les clients chargés au démarrage de la session sont consultables hors ligne.',
        'clear'        => 'Retirer le client',
        'badge-new'    => 'nouveau',

        'create' => [
            'label'   => 'Nouveau client',
            'heading' => 'Nouveau client',
            'created' => ':name ajouté et sélectionné.',

            'fields' => [
                'name'  => 'Nom',
                'email' => 'E-mail',
                'phone' => 'Téléphone',
            ],
        ],
    ],

    'variants' => [
        'heading'     => 'Sélection des attributs',
        'confirm'     => 'Ajouter',
        'unavailable' => "Cette combinaison n'existe pas.",
    ],

    'lots' => [
        'heading'        => 'Numéro(s) de lot/série requis',
        'placeholder'    => 'Numéro de série/lot',
        'add'            => 'Ajouter',
        'remove'         => 'Retirer le numéro',
        'missing'        => 'Définir le numéro de lot / série',
        'none-available' => "Il n'existe aucun numéro de série/lot pour le produit sélectionné, et leur création n'est pas autorisée depuis l'application Point de vente.",

        'warning' => [
            'heading' => 'Certains numéros de série/lot sont manquants',
            'body'    => "Vous essayez de vendre des produits avec des numéros de série/lot, mais certains ne sont pas renseignés.\nSouhaitez-vous continuer malgré tout ?",
            'proceed' => 'OK',
        ],
    ],

    'notes' => [
        'internal'    => 'Note interne',
        'kitchen'     => 'Note cuisine',
        'heading'     => 'Ajouter une note interne',
        'empty'       => 'Aucun modèle de note configuré.',
        'hint'        => "Choisissez d'abord une ligne, puis une note.",
        'placeholder' => 'Ajouter une note pour cette ligne',
    ],

    'money-details' => [
        'label'            => 'Pièces/Billets',
        'opening-heading'  => "Détails d'ouverture :",
        'closing-heading'  => 'Détails de clôture :',
        'total'            => 'Total : :total',
        'confirm'          => 'Confirmer',
        'close'            => 'Fermer',
        'increase'         => 'Ajouter un',
        'decrease'         => 'Retirer un',
    ],

    'cart' => [
        'discount' => ':percentage% de remise',
        'subtotal' => 'Sous-total',
        'tax'      => 'Taxes',
        'rounding' => 'Arrondi',
        'total'    => 'Total',
        'remove'   => 'Retirer :product',

        'empty' => [
            'description' => 'Scannez ou touchez un produit pour commencer',
        ],
    ],

    'catalogue' => [
        'search'          => 'Rechercher des produits',
        'create-product'  => 'Créer un produit',
        'info'            => 'Informations sur le produit :product',
        'info-depleted'   => 'Informations sur le produit :product, aucun disponible',
    ],

    'numpad' => [
        'qty'       => 'Qté',
        'price'     => 'Prix',
        'backspace' => 'Retour arrière',
        'clear'     => 'C',
    ],

    'payment' => [
        'ship-later'                            => 'Livrer plus tard',
        'ship-later-heading'                    => "Sélectionnez la date d'expédition",
        'ship-later-error-heading'              => 'Adresse incorrecte pour la livraison',
        'ship-later-no-customer'                => 'Sélectionnez un client avant de livrer cette commande ultérieurement.',
        'ship-later-no-address'                 => "Le client sélectionné a besoin d'une adresse.",
        'select-method'                         => 'Veuillez sélectionner un mode de paiement',
        'invoice'                               => 'Facture',
        'change'                                => 'monnaie',
        'remove'                                => 'Retirer le paiement :method',
        'validate'                              => 'Valider',
        'max-value-title'                       => 'Valeur maximale atteinte',
        'max-value-body'                        => "Le montant ne peut pas être supérieur au montant dû si vous n'avez pas de mode de paiement en espèces configuré.",
        'customer-required-title'               => 'Client requis',
        'customer-required-body-terminal'       => 'Sélectionnez un client avant de valider cette commande ; ce terminal en exige un.',
        'customer-required-body-payment-method' => 'Sélectionnez un client avant de valider cette commande ; le mode de paiement sélectionné en exige un.',
        'customer-required-body-invoice'        => 'Sélectionnez un client avant de valider cette commande pour générer une facture.',
        'incomplete-title'                      => 'Mode de paiement requis',
        'incomplete-body'                       => 'Sélectionnez un mode de paiement qui couvre le total avant de valider cette commande.',
    ],

    'floor' => [
        'back'      => 'Plan',
        'table'     => 'Table :table',
        'no-table'  => 'Sans table',
        'empty'     => 'Aucune table sur cet étage pour l’instant. Ajoutez-les depuis l’administration.',
        'no-floors' => 'Aucun étage disponible. Ajoutez un nouvel étage pour commencer.',
        'seats'     => '{1} :count place|[2,*] :count places',
        'orders'    => '{1} :count commande|[2,*] :count commandes',
        'guests'    => '{1} :count couvert|[2,*] :count couverts',
    ],

    'guests' => [
        'label'     => 'Couverts',
        'heading'   => 'Nombre de couverts',
        'increase'  => 'Ajouter un couvert',
        'decrease'  => 'Retirer un couvert',
        'per-guest' => ':amount par couvert',
    ],

    'split' => [
        'label'    => 'Partager',
        'heading'  => 'Partage de l’addition',
        'hint'     => 'Touchez une ligne pour déplacer une unité vers la nouvelle addition. Touchez à nouveau pour en déplacer plus.',
        'new-bill' => 'Nouvelle addition',
        'confirm'  => 'Partager la commande',
    ],

    'bill' => [
        'label'   => 'Addition',
        'heading' => 'Impression de l’addition',
    ],

    'takeaway' => [
        'badge'       => 'À emporter',
        'to-takeaway' => 'Passer à emporter',
        'to-dine-in'  => 'Passer sur place',
    ],

    'order-name' => [
        'label'       => 'Modifier le nom de la commande',
        'heading'     => 'Modifier le nom de la commande',
        'placeholder' => 'ex. 18:45 Jean 4P',
    ],

    'tip' => [
        'label'  => 'Pourboire',
        'add'    => 'Ajouter un pourboire',
        'change' => 'Modifier le pourboire',
        'remove' => 'Retirer le pourboire',
    ],

    'global-discount' => [
        'label'   => 'Remise',
        'heading' => 'Pourcentage de remise',
        'apply'   => 'Appliquer',
        'hint'    => 'Remplace toute remise déjà appliquée à la commande.',
    ],

    'booking' => [
        'book'    => 'Réserver la table',
        'release' => 'Libérer la table',
    ],

    'transfer' => [
        'label'        => 'Transférer / Fusionner',
        'prompt'       => 'Sélectionnez une table pour transférer :order',
        'has-payments' => 'Cette commande a des paiements. Supprimez-les avant de la fusionner avec une autre table.',
    ],

    'table-selector' => [
        'label'       => 'Table',
        'heading'     => 'Sélecteur de table',
        'hint'        => 'Saisissez un numéro de table ou un nom pour une commande sans table.',
        'placeholder' => 'Numéro de table ou nom',
        'jump'        => 'Aller',
    ],

    'session-closed' => [
        'heading' => 'Caisse fermée',
        'body'    => 'Cette caisse a été fermée depuis une autre fenêtre ou un autre appareil. Les commandes ouvertes sur cet appareil n’ont pas été envoyées ; rouvrez la caisse pour continuer.',
        'back'    => 'Retour aux caisses',
    ],

    'floor-plan' => [
        'edit'                 => 'Modifier le plan',
        'switch-view'          => 'Changer la vue de l’étage',
        'view-map'             => 'Vue plan',
        'view-grid'            => 'Vue grille',
        'floor-name'           => 'Nom de l’étage',
        'background'           => 'Fond de l’étage',
        'no-colour'            => 'Aucune couleur',
        'add-table'            => 'Ajouter une table',
        'add-floor'            => 'Ajouter un étage',
        'delete-floor'         => 'Supprimer l’étage',
        'confirm-delete-floor' => 'Confirmer la suppression',
        'table-number'         => 'Table',
        'fewer-seats'          => 'Moins de places',
        'more-seats'           => 'Plus de places',
        'make-square'          => 'Rendre carrée',
        'make-round'           => 'Rendre ronde',
        'duplicate'            => 'Dupliquer',
        'delete-table'         => 'Supprimer la table',
        'hint'                 => 'Faites glisser les tables pour les déplacer, la poignée d’angle pour les redimensionner, et touchez une table pour la modifier.',
        'new-floor'            => 'Étage :number',
        'failed'               => 'Impossible d’enregistrer le plan (:status).',
        'add-image'            => 'Ajouter une image',
        'change-image'         => 'Changer l’image',
        'remove-image'         => 'Retirer l’image',
        'zoom-in'              => 'Zoomer',
        'zoom-out'             => 'Dézoomer',
        'fit'                  => 'Ajuster à l’écran',
        'unreachable'          => 'Le serveur est injoignable. Vérifiez la connexion et réessayez.',

        'colours' => [
            'red'        => 'Rouge',
            'orange'     => 'Orange',
            'yellow'     => 'Jaune',
            'green'      => 'Vert',
            'teal'       => 'Bleu canard',
            'blue'       => 'Bleu',
            'violet'     => 'Violet',
            'pink'       => 'Rose',
            'stone'      => 'Pierre',
            'white'      => 'Blanc',
            'light-grey' => 'Gris clair',
            'cream'      => 'Crème',
            'mint'       => 'Menthe',
            'sky'        => 'Ciel',
            'lavender'   => 'Lavande',
            'blush'      => 'Rose pâle',
            'warm-grey'  => 'Gris chaud',
        ],
    ],

    'receipt' => [
        'phone'      => 'Tél. :',
        'served-by'  => 'Servi par :cashier',
        'lot-number' => 'Numéro de lot :numbers',
        'untaxed'    => 'Montant hors taxes',
        'rounding'   => 'Arrondi',
        'total'      => 'TOTAL',
        'change'     => 'Monnaie',
        'order'      => 'Commande :order',
        'new-order'  => 'Nouvelle commande',
        'guests'     => '{1} :count couvert|[2,*] :count couverts',
    ],

    'install' => [
        'installed'   => 'Cette caisse est déjà installée comme application sur cet appareil.',
        'unavailable' => 'Ce navigateur ne peut pas installer la caisse. Chrome ou Edge en HTTPS est requis.',
        'label'       => "Installer l'application",
    ],

    'scanner' => [
        'unsupported'   => 'Ce navigateur ne peut pas scanner avec la caméra. Utilisez plutôt une douchette connectée.',
        'hardware-hint' => 'Une douchette connectée fonctionne partout dans la caisse sans ouvrir ceci.',
        'heading'       => 'Scanner un code-barres',
        'start'         => 'Scanner avec la caméra',
        'stop'          => 'Arrêter',
    ],

    'drafts' => [
        'not-shared' => "Cette commande n'est pas partagée avec les autres caisses",
        'rejected'   => 'Le serveur a refusé cette commande.',
    ],

    'kitchen' => [
        'order'        => 'Commander',
        'new'          => 'Nouveau',
        'cancelled'    => 'Annulé',
        'note-changed' => 'Note modifiée',
        'dine-in'      => 'Sur place',
        'takeaway'     => 'À emporter',
        'to-takeaway'  => 'Sur place → À emporter',
        'to-dine-in'   => 'À emporter → Sur place',
        'by'           => 'Par :name',
        'table'        => 'Table :table',
        'order-note'   => 'Note de commande',
        'offline'      => "L'envoi en cuisine nécessite une connexion.",
        'nothing'      => 'Rien de nouveau à envoyer en cuisine.',
    ],

    'rejected' => [
        'settled-heading' => 'Payé sur une autre caisse',
        'failed-heading'  => "Cette commande n'a pas été enregistrée",
        'order'           => 'Commande :order',
        'table'           => 'Table :table',
        'settled-body'    => ":order a déjà été payée sur une autre caisse. Cette vente n'a pas été enregistrée.",
        'give-back'       => 'Rendez :amount au client.',
        'failed-body'     => ":order n'a pas pu être enregistrée sur le serveur.",
        'returned'        => 'Argent rendu',
        'dismiss'         => 'Ignorer',
    ],

    'offline' => [
        'banner'              => 'Hors ligne — les ventes continuent et se synchronisent au retour de la connexion',
        'waiting'             => ':count commande(s) en attente de synchronisation',
        'rejected'            => ':count commande(s) rejetée(s) par le serveur',
        'storage-unavailable' => "Stockage local indisponible — rechargez la page avant de prendre d'autres commandes",
        'retry'               => 'Réessayer',
        'lost-heading'        => 'Connexion perdue',
        'lost-body'           => "Jusqu'au rétablissement de la connexion, le point de vente fonctionnera avec des fonctionnalités limitées.",
        'continue'            => 'Continuer avec des fonctionnalités limitées',
    ],

    'actions' => [
        'customer' => 'Client',
        'note'     => 'Note',
        'payment'  => 'Paiement',
        'heading'  => 'Actions',
        'label'    => 'Actions',

        'cancel-order' => [
            'label'   => 'Annuler la commande',
            'confirm' => 'Confirmer',
            'hint'    => 'Ses lignes seront perdues. Cette action est irréversible.',
            'failed'  => "Impossible d'annuler la commande (:status).",
            'offline' => "L'annulation de cette commande nécessite une connexion.",
        ],
    ],

    'price-lists' => [
        'label'   => 'Liste de prix',
        'heading' => 'Sélectionner la liste de prix',
        'default' => 'Prix par défaut',
    ],

    'notification' => [
        'success' => [
            'title' => 'Commande terminée',
            'body'  => 'La commande :order a été réglée.',
        ],

        'queued' => [
            'title' => "Commande mise en file d'attente hors ligne",
            'body'  => ':count commande(s) seront synchronisées au retour de la connexion.',
        ],
    ],
];
