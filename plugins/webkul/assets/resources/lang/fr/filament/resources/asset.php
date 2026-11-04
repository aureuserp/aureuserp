<?php

return [
    'title'        => "Actif de l'entreprise",
    'plural-title' => "Actifs de l'entreprise",

    'navigation' => [
        'title' => 'Actifs',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => "Détails de l'actif",
                'fields' => [
                    'name'                       => "Nom de l'actif",
                    'product'                    => 'Produit / Modèle',
                    'product-type'               => 'Type de Produit',
                    'quantity'                   => 'Quantité',
                    'quantity-help'              => 'Quantité totale reçue dans ce lot.',
                    'asset-tag'                  => "Numéro d'actif",
                    'asset-tag-help'             => "Identifiant unique pour l'actif au sein de l'entreprise.",
                    'asset-tag-auto-placeholder' => 'AST-XXXXX (Généré automatiquement)',
                    'serial-no'                  => 'Numéro de série',
                    'model'                      => 'Modèle',
                    'category'                   => 'Catégorie',
                    'condition'                  => 'État',
                    'cost'                       => 'Coût',
                    'purchase-date'              => "Date d'achat",
                    'warranty-until'             => "Garantie jusqu'au",
                    'warehouse'                  => 'Entrepôt',
                    'warehouse-help'             => "Entrepôt d'inventaire où l'actif est stocké lorsqu'il est disponible.",
                    'initial-placement'          => 'Emplacement initial',
                    'placements'                 => [
                        'warehouse'        => "Entrepôt d'inventaire",
                        'site-asset'       => 'Actif de site fixe',
                        'employee-custody' => 'Garde employé',
                    ],
                    'current-site'               => 'Site',
                    'current-employee'           => 'Employé',
                    'note'                       => 'Remarques',
                ],
            ],

            'settings' => [
                'title'  => 'Paramètres',
                'fields' => [
                    'company' => 'Société',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'asset-tag'        => "Numéro d'actif",
            'name'             => "Nom de l'actif",
            'product'          => 'Produit',
            'product-type'     => 'Type de Produit',
            'quantity'         => 'Quantité',
            'model'            => 'Modèle',
            'serial-no'        => 'N° de série',
            'category'         => 'Catégorie',
            'status'           => 'Statut',
            'condition'        => 'État',
            'holder'           => 'Garde actuelle',
            'warehouse'        => 'Entrepôt',
            'current-employee' => 'Employé actuel',
            'current-site'     => 'Site actuel',
            'cost'             => 'Coût',
            'company'          => 'Société',
            'deleted-at'       => 'Archivé le',
            'created-at'       => 'Créé le',
            'updated-at'       => 'Mis à jour le',
        ],

        'groups' => [
            'product'          => 'Produit / Modèle',
            'category'         => 'Catégorie',
            'status'           => 'Statut',
            'condition'        => 'État',
            'warehouse'        => 'Entrepôt',
            'current-employee' => 'Employé actuel',
            'current-site'     => 'Site actuel',
        ],

        'filters' => [
            'status'              => 'Statut',
            'condition'           => 'État',
            'category'            => 'Catégorie',
            'current-holder-type' => 'Type de garde',
            'current-employee'    => 'Employé actuel',
            'department'          => 'Département',
            'warehouse'           => 'Entrepôt',
            'current-site'        => 'Site actuel',
            'site-usage-type'     => 'Utilisation du site',
            'site-type'           => 'Type de site',
            'product'             => 'Produit',
            'product-type'        => 'Type de Produit',
            'company'             => 'Société',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Actif mis à jour',
                    'body'  => "L'actif a été mis à jour avec succès.",
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => "L'actif n'a pas pu être supprimé",
                    ],
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'Exporter',
            ],
        ],
    ],

    'actions' => [
        'issue' => [
            'label' => 'Attribuer la garde',
            'modal' => [
                'heading'                => "Attribuer la garde de l'actif",
                'description'            => 'Attribuer cet actif à un employé ou un site.',
                'consumable-description' => 'Solde disponible : :quantity unités. Les quantités sont déduites de ce lot lors de la remise.',
                'submit'                 => 'Confirmer l\'attribution',
            ],
            'fields' => [
                'allocation-mode'    => "Mode d'attribution",
                'allocation-modes'   => [
                    'employee-custody' => 'Garde employé',
                    'site-asset'       => 'Actif de site fixe',
                ],
                'holder-type'        => 'Type de garde',
                'employee'           => 'Employé',
                'employees'          => 'Destinataires',
                'departments'        => 'Départements',
                'departments-help'   => 'Sélection groupée des destinataires par département. Choisir un site ou des départements remplit la liste automatiquement.',
                'recipients-summary' => ':count destinataire(s) — :total unité(s) au total',
                'site'               => 'Site',
                'receiver'           => 'Réceptionnaire sur le terrain',
                'condition-out'      => 'État initial',
                'quantity'           => 'Quantité par destinataire',
                'quantity-help'      => 'Chaque destinataire sélectionné reçoit cette quantité. Le solde du lot est réduit en conséquence.',
                'issued-at'          => "Date et heure d'attribution",
                'expected-return-at' => 'Retour prévu',
                'issue-note'         => "Note d'attribution",
                'issue-attachments'  => 'Pièces jointes de remise',
            ],
            'notification' => [
                'title' => 'Garde attribuée',
                'body'  => "La garde de l'actif a été attribuée avec succès.",
            ],
        ],

        'transfer' => [
            'label' => 'Transférer la garde',
            'modal' => [
                'heading'     => "Transférer la garde de l'actif",
                'description' => 'Transférer cet actif vers un nouvel employé ou site.',
                'submit'      => 'Confirmer le transfert',
            ],
            'fields' => [
                'allocation-mode'    => "Mode d'attribution",
                'allocation-modes'   => [
                    'employee-custody' => 'Garde employé',
                    'site-asset'       => 'Actif de site fixe',
                ],
                'holder-type'        => 'Nouveau type de garde',
                'employee'           => 'Nouvel employé',
                'site'               => 'Nouveau site',
                'receiver'           => 'Réceptionnaire sur le terrain',
                'condition-in'       => 'État du détenteur précédent',
                'issued-at'          => 'Date et heure de transfert',
                'expected-return-at' => 'Retour prévu',
                'issue-note'         => 'Note de transfert',
                'return-note'        => 'Note de retour du détenteur précédent',
                'return-attachments' => 'Pièces jointes de retour précédent',
                'issue-attachments'  => 'Pièces jointes de nouvelle remise',
            ],
            'notification' => [
                'title' => 'Garde transférée',
                'body'  => "La garde de l'actif a été transférée avec succès.",
            ],
        ],

        'return' => [
            'label' => 'Retourner au stock',
            'modal' => [
                'heading'     => "Retourner l'actif à l'inventaire",
                'description' => "Réceptionner et vérifier l'actif dans l'inventaire disponible.",
                'submit'      => 'Confirmer le retour',
            ],
            'fields' => [
                'condition-in'       => 'État au retour',
                'ended-at'           => 'Date et heure de retour',
                'warehouse'          => 'Entrepôt de réception',
                'return-note'        => 'Note de retour',
                'return-attachments' => 'Pièces jointes de retour',
            ],
            'notification' => [
                'title' => 'Actif retourné',
                'body'  => "L'actif a été retourné à l'inventaire disponible.",
            ],
        ],

        'scrap' => [
            'label' => 'Mettre au rebut',
            'modal' => [
                'heading'     => "Mettre l'actif au rebut",
                'description' => 'Marquer cet actif comme mis au rebut. Cela clôture toute garde en cours.',
                'submit'      => 'Confirmer la mise au rebut',
            ],
            'fields' => [
                'ended-at'    => 'Date et heure de mise au rebut',
                'note'        => 'Motif / Remarque de mise au rebut',
                'attachments' => 'Pièces jointes de mise au rebut',
            ],
            'notification' => [
                'title' => 'Actif mis au rebut',
                'body'  => "L'actif a été marqué comme mis au rebut.",
            ],
        ],

        'mark-lost' => [
            'label' => 'Marquer comme perdu',
            'modal' => [
                'heading'     => "Marquer l'actif comme perdu",
                'description' => 'Marquer cet actif comme perdu. Cela clôture toute garde en cours.',
                'submit'      => 'Confirmer la perte',
            ],
            'fields' => [
                'ended-at'    => 'Date et heure de signalement',
                'note'        => 'Détails du signalement / perte',
                'attachments' => 'Pièces jointes de signalement',
            ],
            'notification' => [
                'title' => 'Actif marqué comme perdu',
                'body'  => "L'actif a été marqué comme perdu.",
            ],
        ],

        'bulk-register' => [
            'label' => 'Enregistrement en masse',
            'modal' => [
                'heading'     => "Enregistrer des unités d'actifs en masse",
                'description' => 'Enregistrer plusieurs unités du même modèle en un seul lot.',
                'submit'      => 'Enregistrer les unités',
            ],
            'sections' => [
                'shared'  => 'Détails communs du lot',
                'mode'    => 'Mode de suivi et saisie',
                'preview' => 'Aperçu de validation',
            ],
            'fields' => [
                'company'             => 'Société',
                'product'             => 'Produit / Modèle',
                'name'                => "Nom de l'actif",
                'model'               => 'Modèle',
                'category'            => 'Catégorie',
                'condition'           => 'État initial',
                'cost'                => 'Coût unitaire',
                'purchase-date'       => "Date d'achat",
                'warranty-until'      => "Garantie jusqu'au",
                'warehouse'           => 'Entrepôt',
                'warehouse-help'      => "Entrepôt d'inventaire initial pour les actifs disponibles enregistrés.",
                'initial-placement'   => 'Emplacement initial',
                'placements'          => [
                    'warehouse'        => "Entrepôt d'inventaire",
                    'site-asset'       => 'Actif de site fixe',
                    'employee-custody' => 'Garde employé',
                ],
                'site'                => 'Site',
                'employee'            => 'Employé',
                'attachments'         => 'Pièces jointes de remise',
                'note'                => 'Note du lot',
                'tracking-mode'       => 'Mode de suivi',
                'modes'               => [
                    'with-serials'    => 'Unités sérialisées (suivies par n° de série)',
                    'without-serials' => 'Unités non sérialisées (chaises, outils, etc.)',
                ],
                'serials-raw'         => 'Liste des numéros de série (un par ligne ou séparés par des virgules)',
                'serials-raw-help'    => 'Collez les numéros : un par ligne ou TAG,NUMERO_SERIE.',
                'parse-to-table'      => 'Convertir en tableau',
                'items'               => 'Entrées d\'unités',
                'serial-no'           => 'Numéro de série',
                'asset-tag'           => 'Étiquette d\'actif',
                'quantity'            => 'Quantité à générer',
                'tags-raw'            => "Numéros d'actifs préimprimés",
                'tags-raw-help'       => "Un numéro d'actif par ligne pour les unités non sérialisées.",
                'unit-note'           => "Note de l'unité",
            ],
            'preview' => [
                'valid'      => ':count unités valides',
                'duplicates' => ':count doublons dans la liste',
                'collisions' => ':count existants en base',
            ],
            'notification' => [
                'title' => 'Enregistrement en masse réussi',
                'body'  => ":count unités d'actifs ont été enregistrées avec succès.",
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'identity' => [
                'title'   => "Identité de l'actif",
                'entries' => [
                    'asset-tag' => "Numéro d'actif",
                    'name'      => "Nom de l'actif",
                    'product'   => 'Produit',
                    'model'     => 'Modèle',
                    'category'  => 'Catégorie',
                    'serial-no' => 'Numéro de série',
                    'status'    => 'Statut',
                    'condition' => 'État',
                ],
            ],

            'custody' => [
                'title'   => 'Garde actuelle',
                'entries' => [
                    'current-employee'   => 'Employé actuel',
                    'current-site'       => 'Site actuel',
                    'site-usage'         => 'Utilisation du site',
                    'warehouse'          => 'Entrepôt',
                    'initial-quantity'   => 'Quantité initiale',
                    'available-quantity' => 'Quantité disponible',
                    'issued-at'          => "Date d'attribution",
                    'expected-return-at' => 'Retour prévu',
                    'custody-note'       => 'Note de garde',
                ],
            ],

            'acquisition' => [
                'title'   => 'Acquisition et finances',
                'entries' => [
                    'company'        => 'Société',
                    'purchase-date'  => "Date d'achat",
                    'warranty-until' => "Garantie jusqu'au",
                    'cost'           => 'Coût',
                    'creator'        => 'Créé par',
                ],
            ],

            'notes' => [
                'title'   => 'Remarques',
                'entries' => [
                    'note' => 'Remarques',
                ],
            ],

            'audit' => [
                'title'   => 'Audit',
                'entries' => [
                    'created-at' => 'Créé le',
                    'updated-at' => 'Mis à jour le',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'assignments' => [
            'title'   => 'Historique de garde et mouvements',
            'columns' => [
                'occurred-at'        => 'Date et heure',
                'type'               => 'Type de mouvement',
                'source-type'        => 'Origine',
                'from-display'       => 'De',
                'to-display'         => 'À',
                'condition'          => 'État',
                'performed-by'       => 'Effectué par',
                'expected-return-at' => 'Retour prévu',
                'note'               => 'Note',
                'attachments'        => 'Pièces jointes',
            ],
            'filters' => [
                'type'        => 'Type de mouvement',
                'source-type' => 'Origine',
            ],
            'view-modal' => [
                'heading'  => 'Détails du mouvement',
                'sections' => [
                    'details'     => 'Informations du mouvement',
                    'attachments' => 'Documents et photos joints',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'Nouvel actif',
                    'notification' => [
                        'title' => 'Actif créé',
                        'body'  => "L'actif a été créé avec succès.",
                    ],
                ],
            ],
            'tabs' => [
                'all'            => 'Tous',
                'available'      => 'Disponible',
                'consumables'    => 'Consommables',
                'site-assets'    => 'Actifs de site fixe',
                'with-employees' => 'Chez les employés',
                'at-sites'       => 'Sur sites / entrepôts',
                'in-the-field'   => 'Sur le terrain',
                'overdue-return' => 'Retour en retard',
                'scrapped-lost'  => 'Mis au rebut / Perdu',
                'archived'       => 'Archivé',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Actif créé',
                'body'  => "L'actif a été créé avec succès.",
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Actif mis à jour',
                'body'  => "L'actif a été mis à jour avec succès.",
            ],
        ],
    ],
];
