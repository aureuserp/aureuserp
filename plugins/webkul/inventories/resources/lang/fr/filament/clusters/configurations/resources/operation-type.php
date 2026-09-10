<?php

return [
    'navigation' => [
        'title' => 'Types d\'opération',
        'group' => 'Gestion des entrepôts',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'operator-type'             => 'Type d\'opération',
                    'operator-type-placeholder' => 'ex. Réceptions',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Applicable sur',
                'description' => 'Sélectionnez les emplacements où cette route peut être sélectionnée.',

                'fields' => [
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'operator-type'                      => 'Type d\'opération',
                    'sequence-prefix'                    => 'Préfixe de séquence',
                    'generate-shipping-labels'           => 'Générer les étiquettes d\'expédition',
                    'warehouse'                          => 'Entrepôt',
                    'show-reception-report'              => 'Afficher le rapport de réception à la validation',
                    'show-reception-report-hint-tooltip' => 'Si sélectionné, le système affichera automatiquement le rapport de réception lors de la validation, à condition qu\'il y ait des mouvements à allouer.',
                    'company'                            => 'Société',
                    'return-type'                        => 'Type de retour',
                    'create-backorder'                   => 'Créer un reliquat',
                    'move-type'                          => 'Type de mouvement',
                    'move-type-hint-tooltip'             => 'À moins qu\'il ne soit défini par le document source, ceci servira de politique de prélèvement par défaut pour ce type d\'opération.',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title'  => 'Numéros de lot/série',

                        'fields' => [
                            'create-new'                => 'Créer nouveau',
                            'create-new-hint-tooltip'   => 'Si sélectionné, le système supposera que vous souhaitez créer de nouveaux numéros de lot/série, vous permettant de les saisir dans un champ de texte.',
                            'use-existing'              => 'Utiliser existant',
                            'use-existing-hint-tooltip' => 'Si sélectionné, vous pouvez choisir les numéros de lot/série ou choisir de ne pas en attribuer. Cela permet de créer du stock sans lot ou sans restriction sur le lot utilisé.',
                        ],
                    ],

                    'locations' => [
                        'title'  => 'Emplacements',

                        'fields' => [
                            'source-location'                   => 'Emplacement source',
                            'source-location-hint-tooltip'      => 'Il s\'agit de l\'emplacement source par défaut lors de la création manuelle de cette opération. Il peut toutefois être modifié ultérieurement, et les routes peuvent attribuer un autre emplacement par défaut.',
                            'destination-location'              => 'Emplacement de destination',
                            'destination-location-hint-tooltip' => 'Il s\'agit de l\'emplacement de destination par défaut pour les opérations créées manuellement. Il peut toutefois être modifié ultérieurement, et les routes peuvent attribuer un autre emplacement par défaut.',
                        ],
                    ],

                    'packages' => [
                        'title'  => 'Colis',

                        'fields' => [
                            'show-entire-package'              => 'Déplacer le colis entier',
                            'show-entire-package-hint-tooltip' => 'Si sélectionné, vous pouvez déplacer des colis entiers.',
                        ],
                    ],
                ],
            ],

            'hardware' => [
                'title'  => 'Matériel',

                'fieldsets' => [
                    'print-on-validation' => [
                        'title'  => 'Imprimer à la validation',

                        'fields' => [
                            'delivery-slip'              => 'Bon de livraison',
                            'delivery-slip-hint-tooltip' => 'Si sélectionné, le système imprimera automatiquement le bon de livraison lorsque le prélèvement est validé.',

                            'return-slip'              => 'Bon de retour',
                            'return-slip-hint-tooltip' => 'Si sélectionné, le système imprimera automatiquement le bon de retour lorsque le prélèvement est validé.',

                            'product-labels'              => 'Étiquettes produit',
                            'product-labels-hint-tooltip' => 'Si sélectionné, le système imprimera automatiquement les étiquettes produit lorsque le prélèvement est validé.',

                            'lots-labels'              => 'Étiquettes de lot/numéro de série',
                            'lots-labels-hint-tooltip' => 'Si sélectionné, le système imprimera automatiquement les étiquettes de lot/numéro de série lorsque le prélèvement est validé.',

                            'reception-report'              => 'Rapport de réception',
                            'reception-report-hint-tooltip' => 'Si sélectionné, le système imprimera automatiquement le rapport de réception lorsque le prélèvement est validé et contient des mouvements attribués.',

                            'reception-report-labels'              => 'Étiquettes du rapport de réception',
                            'reception-report-labels-hint-tooltip' => 'Si sélectionné, le système imprimera automatiquement les étiquettes du rapport de réception lorsque le prélèvement est validé.',

                            'package-content'              => 'Contenu du colis',
                            'package-content-hint-tooltip' => 'Si sélectionné, le système imprimera automatiquement les détails du colis et son contenu lorsque le prélèvement est validé.',
                        ],
                    ],

                    'print-on-pack' => [
                        'title'  => 'Imprimer lors de « Mettre en colis »',

                        'fields' => [
                            'package-label'              => 'Étiquette de colis',
                            'package-label-hint-tooltip' => 'Si sélectionné, le système imprimera automatiquement l\'étiquette de colis lorsque le bouton « Mettre en colis » est utilisé.',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'warehouse'  => 'Entrepôt',
            'company'    => 'Société',
            'deleted-at' => 'Supprimé le',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'groups' => [
            'type'       => 'Type',
            'warehouse'  => 'Entrepôt',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'filters' => [
            'type'      => 'Type',
            'warehouse' => 'Entrepôt',
            'company'   => 'Société',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Type d\'opération restauré',
                    'body'  => 'Le type d\'opération a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Type d\'opération supprimé',
                    'body'  => 'Le type d\'opération a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Type d\'opération définitivement supprimé',
                        'body'  => 'Le type d\'opération a été définitivement supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'Le type d\'opération n\'a pas pu être supprimé',
                        'body'  => 'Le type d\'opération ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Types d\'opération restaurés',
                    'body'  => 'Les types d\'opération ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Types d\'opération supprimés',
                    'body'  => 'Les types d\'opération ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Types d\'opération définitivement supprimés',
                        'body'  => 'Les types d\'opération ont été définitivement supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les types d\'opération n\'ont pas pu être supprimés',
                        'body'  => 'Les types d\'opération ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                    ],
                ],
            ],
        ],

        'empty-actions' => [
            'create' => [
                'label' => 'Créer un type d\'opération',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informations générales',

                'entries' => [
                    'name' => 'Nom',
                ],
            ],

            'record-information' => [
                'title' => 'Informations sur l\'enregistrement',

                'entries' => [
                    'created-by'   => 'Créé par',
                    'created-at'   => 'Créé le',
                    'last-updated' => 'Dernière mise à jour',
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title' => 'Général',

                'entries' => [
                    'type'                       => 'Type d\'opération',
                    'sequence_code'              => 'Code de séquence',
                    'print_label'                => 'Imprimer l\'étiquette',
                    'warehouse'                  => 'Entrepôt',
                    'reservation_method'         => 'Méthode de réservation',
                    'auto_show_reception_report' => 'Affichage automatique du rapport de réception',
                    'company'                    => 'Société',
                    'return_operation_type'      => 'Type d\'opération de retour',
                    'create_backorder'           => 'Créer un reliquat',
                    'move_type'                  => 'Type de mouvement',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title' => 'Lots',

                        'entries' => [
                            'use_create_lots'   => 'Utiliser la création de lots',
                            'use_existing_lots' => 'Utiliser les lots existants',
                        ],
                    ],

                    'locations' => [
                        'title' => 'Emplacements',

                        'entries' => [
                            'source_location'      => 'Emplacement source',
                            'destination_location' => 'Emplacement de destination',
                        ],
                    ],
                ],
            ],
            'hardware' => [
                'title' => 'Matériel',

                'fieldsets' => [
                    'print_on_validation' => [
                        'title' => 'Imprimer à la validation',

                        'entries' => [
                            'auto_print_delivery_slip'           => 'Impression automatique du bon de livraison',
                            'auto_print_return_slip'             => 'Impression automatique du bon de retour',
                            'auto_print_product_labels'          => 'Impression automatique des étiquettes produit',
                            'auto_print_lot_labels'              => 'Impression automatique des étiquettes de lot',
                            'auto_print_reception_report'        => 'Impression automatique du rapport de réception',
                            'auto_print_reception_report_labels' => 'Impression automatique des étiquettes du rapport de réception',
                            'auto_print_packages'                => 'Impression automatique des colis',
                        ],
                    ],

                    'print_on_pack' => [
                        'title' => 'Imprimer sur colis',

                        'entries' => [
                            'auto_print_package_label' => 'Impression automatique de l\'étiquette de colis',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
