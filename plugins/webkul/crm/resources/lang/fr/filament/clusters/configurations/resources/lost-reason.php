<?php

return [
    'model-label'        => 'Motif de perte',
    'plural-model-label' => 'Motifs de perte',

    'navigation' => [
        'title' => 'Motifs de perte',
        'group' => 'Pipeline',
    ],

    'form' => [
        'fields' => [
            'name' => 'Description',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Description',
            'created-by' => 'Créé par',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Motif de perte mis à jour',
                    'body'  => 'Le motif de perte a été mis à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Motif de perte restauré',
                    'body'  => 'Le motif de perte a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'label' => 'Archiver',

                'modal' => [
                    'heading'     => 'Archiver le motif de perte',
                    'description' => 'Le motif de perte sera déplacé dans l\'onglet Archivés. Vous pouvez le restaurer à tout moment.',
                ],

                'notification' => [
                    'title' => 'Motif de perte archivé',
                    'body'  => 'Le motif de perte a été archivé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Motif de perte supprimé',
                    'body'  => 'Le motif de perte a été définitivement supprimé.',
                ],
            ],
        ],
    ],
];
