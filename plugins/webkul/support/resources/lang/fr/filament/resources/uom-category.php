<?php

return [
    'navigation' => [
        'title' => 'Catégories UdM',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'name' => 'Nom',
                ],
            ],

            'uoms' => [
                'title' => 'Unités de mesure',

                'fields' => [
                    'uoms'     => 'Unités',
                    'type'     => 'Type',
                    'name'     => 'Unité de mesure',
                    'ratio'    => 'Ratio',
                    'rounding' => 'Précision d\'arrondi',
                ],

                'validations' => [
                    'missing-reference'          => 'Cette catégorie doit avoir une unité de mesure de référence.',
                    'multiple-references'        => "Cette catégorie ne doit avoir qu'une seule unité de mesure de référence.",
                    'ratio-greater-than-zero'    => 'Le ratio de conversion d\'une unité de mesure ne peut pas être égal à zéro.',
                    'rounding-greater-than-zero' => 'La précision d\'arrondi doit être strictement positive.',
                ],

                'actions' => [
                    'add' => 'Ajouter une unité',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'uoms'       => 'UdM',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'groups' => [
            'created-at' => 'Créé le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Catégorie UdM mise à jour',
                    'body'  => 'La catégorie UdM a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Catégorie UdM supprimée',
                    'body'  => 'La catégorie UdM a été supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Catégories UdM supprimées',
                    'body'  => 'Les catégories UdM ont été supprimées avec succès.',
                ],
            ],
        ],
    ],
];
