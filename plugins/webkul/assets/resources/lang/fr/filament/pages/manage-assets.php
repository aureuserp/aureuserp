<?php

return [
    'title' => 'Paramètres des actifs',

    'navigation' => [
        'title' => 'Actifs et étiquetage',
        'group' => 'Actifs',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'       => 'Configuration générale des actifs',
                'description' => 'Gérer l\'identification par défaut des actifs, les séquences et les paramètres opérationnels.',
            ],
            'warehouses_and_operation' => [
                'title'       => 'Entrepôts et modèles opérationnels',
                'description' => 'Configurer les entrepôts par défaut et gérer les affectations d\'actifs fixes aux sites.',
            ],
            'custody_and_duration' => [
                'title'       => 'Règles de garde et durées',
                'description' => 'Définir les durées de garde, dates de retour prévues et restrictions d\'attribution.',
            ],
            'governance_and_attachments' => [
                'title'       => 'Documentation et gouvernance de mise au rebut',
                'description' => 'Exiger des pièces jointes pour les transitions de garde et encadrer la mise au rebut.',
            ],
            'consumables_rules' => [
                'title'       => 'Émission de consommables et quantités',
                'description' => 'Contrôler l\'émission par lot, la sélection automatique des destinataires, les quantités par défaut et les exigences de site ou de pièces jointes pour les consommables.',
            ],
        ],
        'fields' => [
            'tag-prefix'                              => 'Préfixe de l\'étiquette d\'actif',
            'auto-generate-tag'                       => 'Générer automatiquement l\'étiquette',
            'tag-padding'                             => 'Nombre de chiffres séquentiels',
            'default-condition'                       => 'État initial par défaut',
            'require-serial-by-default'               => 'Exiger le numéro de série par défaut',
            'default-warehouse'                       => 'Entrepôt par défaut',
            'enable-site-assets'                      => 'Activer les actifs de site fixe',
            'require-site-on-custody'                 => 'Exiger un site lors de la remise',
            'default-custody-duration-days'           => 'Durée de garde par défaut (jours)',
            'require-expected-return-date'            => 'Exiger une date de retour prévue',
            'prevent-duplicate-product-custody'       => 'Empêcher la garde multiple du même produit',
            'require-attachments-on-custody'          => 'Exiger des pièces jointes lors de la remise',
            'enable-consumables-batch-issue'          => 'Activer l\'émission groupée des consommables',
            'auto-fill-consumable-employees-by-site'  => 'Préremplir les destinataires depuis le site',
            'default-consumable-issue-quantity'       => 'Quantité émise par défaut par destinataire',
            'require-site-on-consumable-issue'        => 'Exiger un site lors de l\'émission de consommables',
            'require-attachments-on-consumable-issue' => 'Exiger des pièces jointes lors de l\'émission de consommables',
            'require-attachments-on-damage'           => 'Exiger des pièces jointes en cas de dommage',
            'require-attachments-on-termination'      => 'Exiger des pièces jointes pour la mise au rebut',
            'restrict-termination-to-managers'        => 'Restreindre la mise au rebut aux responsables',
            'termination-approval-cost-threshold'     => 'Seuil de coût d\'approbation de mise au rebut',
        ],
        'helpers' => [
            'tag-prefix'                              => 'Préfixe utilisé lors de la génération automatique d\'étiquettes (ex. AST-).',
            'tag-prefix-locked'                       => 'Le préfixe est verrouillé car des actifs ont déjà été enregistrés.',
            'auto-generate-tag'                       => 'Attribuer automatiquement des étiquettes séquentielles aux nouveaux actifs.',
            'tag-padding'                             => 'Nombre de chiffres pour le numéro séquentiel (3 à 8). Aperçu : :preview',
            'default-condition'                       => 'État initial attribué aux actifs nouvellement enregistrés.',
            'require-serial-by-default'               => 'Exiger obligatoirement un numéro de série lors de l\'enregistrement d\'une unité.',
            'default-warehouse'                       => 'Entrepôt d\'entreprise par défaut attribué aux actifs disponibles.',
            'manage-warehouses-link'                  => 'Gérer les entrepôts',
            'enable-site-assets'                      => 'Permettre d\'affecter directement des actifs à des sites fixes sans employé désigné.',
            'manage-sites-link'                       => 'Gérer les sites',
            'require-site-on-custody'                 => 'Exiger la sélection d\'un site lors de la remise d\'un actif à un employé.',
            'default-custody-duration-days'           => 'Nombre de jours ajoutés à la date d\'émission pour calculer la date de retour prévue.',
            'require-expected-return-date'            => 'Exiger une date de retour prévue lors de toute remise à un employé.',
            'prevent-duplicate-product-custody'       => 'Empêcher d\'attribuer plusieurs actifs du même produit au même employé simultanément.',
            'require-attachments-on-custody'          => 'Exiger des bons de livraison ou récépissés lors des émissions et transferts.',
            'enable-consumables-batch-issue'          => 'Autoriser l\'émission d\'un lot de consommables à plusieurs destinataires en une seule opération.',
            'auto-fill-consumable-employees-by-site'  => 'Remplir automatiquement la liste des destinataires avec les employés actifs du site sélectionné.',
            'default-consumable-issue-quantity'       => 'Quantité déduite par destinataire lors de l\'émission de consommables, de 1 à 10000.',
            'require-site-on-consumable-issue'        => 'Exiger la sélection d\'un site de garde lors de l\'émission de consommables, indépendamment des règles d\'actifs.',
            'require-attachments-on-consumable-issue' => 'Exiger la pièce justificative de remise lors de l\'émission de consommables, indépendamment des règles d\'actifs.',
            'require-attachments-on-damage'           => 'Exiger des photos ou justificatifs en cas de retour d\'actif endommagé.',
            'require-attachments-on-termination'      => 'Exiger des documents officiels pour la mise au rebut ou la perte.',
            'restrict-termination-to-managers'        => 'Exiger des permissions de responsable (Groupe ou Global) pour mettre au rebut ou déclarer perdu.',
            'termination-approval-cost-threshold'     => 'Les actifs dont le coût est inférieur à ce seuil peuvent être mis au rebut sans approbation. Laisser vide pour tous les montants.',
        ],
    ],

    'warnings' => [
        'site-assets-held' => 'Impossible de désactiver les actifs de site tant que des actifs sont affectés à des sites fixes.',
    ],
];
