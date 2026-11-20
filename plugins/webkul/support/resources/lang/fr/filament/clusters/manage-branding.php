<?php

return [
    'breadcrumb' => 'Image de marque',
    'title'      => 'Image de marque',
    'group'      => 'Général',

    'navigation' => [
        'label' => 'Image de marque',
    ],

    'form' => [
        'tabs' => [
            'colors' => 'Couleurs de la marque',
            'logo'   => 'Logo et favicon',
        ],

        'sections' => [
            'colors' => [
                'title'       => 'Couleurs de la marque',
                'description' => "Définissez les couleurs utilisées dans les panneaux d'administration et client. Cliquez sur l'icône d'information de n'importe quelle couleur pour prévisualiser son apparence avant d'enregistrer.",
            ],
            'palettes' => [
                'title'       => 'Palettes de couleurs suggérées',
                'description' => 'Choisissez parmi nos palettes de couleurs prédéfinies ou créez la vôtre.',
            ],
            'logo' => [
                'title'       => 'Logo et favicon',
                'description' => "Remplacez les logos et le favicon utilisés dans les panneaux d'administration et client. Laissez un champ vide pour conserver la valeur par défaut.",
            ],
        ],

        'fields' => [
            'light-logo'         => 'Logo clair',
            'light-logo-helper'  => 'Affiché sur les fonds clairs. Remplace le logo par défaut.',
            'dark-logo'          => 'Logo sombre',
            'dark-logo-helper'   => 'Affiché lorsque le mode sombre est activé.',
            'favicon'            => 'Favicon',
            'favicon-helper'     => "Icône de l'onglet du navigateur.",
            'logo-height'        => 'Hauteur du logo',
            'logo-height-helper' => 'Une valeur de hauteur CSS, par exemple 2rem ou 40px.',
            'primary-color'      => 'Principale',
            'gray-color'         => 'Gris',
            'danger-color'       => 'Danger',
            'info-color'         => 'Info',
            'success-color'      => 'Succès',
            'warning-color'      => 'Avertissement',
        ],

        'palettes' => [
            'coral'       => 'Rouge corail',
            'ocean'       => 'Bleu océan',
            'forest'      => 'Vert forêt',
            'purple'      => 'Rêve violet',
            'modern'      => 'Gris moderne',
            'apply'       => 'Utiliser celle-ci',
            'custom-hint' => "Besoin d'autre chose ? Définissez vos propres couleurs avec les sélecteurs ci-dessus.",
        ],

        'preview' => [
            'title'             => 'Aperçu en direct',
            'heading'           => 'Aperçu des couleurs',
            'description'       => "Prévisualisez l'apparence de vos couleurs dans toute l'interface avant d'enregistrer.",
            'hint'              => "Cliquez sur l'icône d'information de n'importe quelle couleur pour voir un aperçu en direct de l'apparence des couleurs sélectionnées dans toute l'application.",
            'shade-note'        => 'Nuance 600 (utilisée pour les boutons)',
            'light'             => 'Thème clair',
            'dark'              => 'Thème sombre',
            'search'            => 'Rechercher...',
            'primary-button'    => 'Bouton principal',
            'secondary-button'  => 'Bouton secondaire',
            'alert'             => "Ceci est un message d'information.",
            'card-title'        => 'Titre de la carte',
            'card-body'         => 'Voici un exemple de contenu pour montrer comment le texte et les surfaces apparaissent avec les couleurs sélectionnées.',
            'link'              => 'Voir les détails',
            'input-label'       => "Texte de l'en-tête",
            'input-placeholder' => 'Champ de saisie',

            'nav' => [
                'dashboard' => 'Tableau de bord',
                'products'  => 'Produits',
                'orders'    => 'Commandes',
                'customers' => 'Clients',
                'settings'  => 'Paramètres',
            ],

            'status' => [
                'success' => 'Succès',
                'info'    => 'Info',
                'warning' => 'Avertissement',
                'danger'  => 'Danger',
            ],
        ],
    ],

    'actions' => [
        'reset' => [
            'label' => 'Réinitialiser par défaut',
        ],
    ],
];
