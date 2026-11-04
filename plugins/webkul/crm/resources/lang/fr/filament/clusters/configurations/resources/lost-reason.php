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

            'force-delete' => [
                'notification' => [
                    'title' => 'Motif de perte supprimé',
                    'body'  => 'Le motif de perte a été définitivement supprimé.',
                ],
            ],
        ],
    ],
];
