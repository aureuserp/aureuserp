<?php

return [
    'title' => 'Étapes',

    'navigation' => [
        'title' => 'Étapes',
        'group' => 'Postes',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informations générales',

                'fields' => [
                    'stage-name'   => 'Nom de l\'étape',
                    'sort'         => 'Ordre de séquence',
                    'requirements' => 'Exigences',
                ],
            ],

            'tooltips' => [
                'title'       => 'Infobulles',
                'description' => 'Définissez le libellé personnalisé pour le statut de la candidature.',

                'fields' => [
                    'gray-label'          => 'Libellé gris',
                    'gray-label-tooltip'  => 'Le libellé pour le statut gris.',
                    'red-label'           => 'Libellé rouge',
                    'red-label-tooltip'   => 'Le libellé pour le statut rouge.',
                    'green-label'         => 'Libellé vert',
                    'green-label-tooltip' => 'Le libellé pour le statut vert.',
                ],
            ],

            'additional-information' => [
                'title' => 'Informations complémentaires',

                'fields' => [
                    'job-positions' => 'Postes',
                    'folded'        => 'Replié',
                    'hired-stage'   => 'Étape d\'embauche',
                    'default-stage' => 'Étape par défaut',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Nom de l\'étape',
            'hired-stage'        => 'Étape d\'embauche',
            'default-stage'      => 'Étape par défaut',
            'folded'             => 'Replié',
            'job-positions'      => 'Postes',
            'created-by'         => 'Créé par',
            'created-at'         => 'Créé le',
            'updated-at'         => 'Mis à jour le',
        ],

        'filters' => [
            'name'         => 'Nom de l\'étape',
            'job-position' => 'Poste',
            'folded'       => 'Replié',
            'gray-label'   => 'Libellé gris',
            'red-label'    => 'Libellé rouge',
            'green-label'  => 'Libellé vert',
            'created-by'   => 'Créé par',
            'created-at'   => 'Créé le',
            'updated-at'   => 'Mis à jour le',
        ],

        'groups' => [
            'job-position' => 'Poste',
            'stage-name'   => 'Nom de l\'étape',
            'folded'       => 'Replié',
            'gray-label'   => 'Libellé gris',
            'red-label'    => 'Libellé rouge',
            'green-label'  => 'Libellé vert',
            'created-by'   => 'Créé par',
            'created-at'   => 'Créé le',
            'updated-at'   => 'Mis à jour le',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Étapes supprimées',
                        'body'  => 'Les étapes ont été supprimées avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les étapes n\'ont pas pu être supprimées',
                        'body'  => 'Les étapes ne peuvent pas être supprimées car elles sont actuellement utilisées.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Étapes supprimées',
                    'body'  => 'Les étapes ont été supprimées avec succès.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Nouvelle étape',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informations générales',

                'entries' => [
                    'stage-name'   => 'Nom de l\'étape',
                    'sort'         => 'Ordre de séquence',
                    'requirements' => 'Exigences',
                ],
            ],

            'tooltips' => [
                'title'       => 'Infobulles',
                'description' => 'Définissez le libellé personnalisé pour le statut de la candidature.',

                'entries' => [
                    'gray-label'          => 'Libellé gris',
                    'gray-label-tooltip'  => 'Le libellé pour le statut gris.',
                    'red-label'           => 'Libellé rouge',
                    'red-label-tooltip'   => 'Le libellé pour le statut rouge.',
                    'green-label'         => 'Libellé vert',
                    'green-label-tooltip' => 'Le libellé pour le statut vert.',
                ],
            ],

            'additional-information' => [
                'title' => 'Informations complémentaires',

                'entries' => [
                    'job-positions'      => 'Poste',
                    'folded'             => 'Replié',
                    'hired-stage'        => 'Étape d\'embauche',
                    'default-stage'      => 'Étape par défaut',
                ],
            ],
        ],
    ],

];
