<?php

return [
    'title' => 'Variantes',

    'form' => [
        'date'                   => 'Date',
        'employee'               => 'Employé',
        'description'            => 'Description',
        'time-spent'             => 'Temps passé',
        'time-spent-helper-text' => 'Temps passé en heures (ex. 1,5 heure signifie 1 heure 30 minutes)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'Date',
            'employee'               => 'Employé',
            'description'            => 'Description',
            'time-spent'             => 'Temps passé',
            'time-spent-on-subtasks' => 'Temps passé sur les sous-tâches',
            'total-time-spent'       => 'Temps total passé',
            'remaining-time'         => 'Temps restant',
            'variant-values'         => 'Valeurs de la variante',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Variante supprimée',
                    'body'  => 'La variante a été supprimée avec succès.',
                ],
            ],

            'view' => [
                'extra-footer-actions' => [
                    'print' => [
                        'label' => 'Imprimer les étiquettes',

                        'form' => [
                            'fields' => [
                                'quantity' => 'Nombre d\'étiquettes',
                                'format'   => 'Format',

                                'format-options' => [
                                    'dymo'       => 'Dymo',
                                    '2x7_price'  => '2x7 avec prix',
                                    '4x7_price'  => '4x7 avec prix',
                                    '4x12'       => '4x12',
                                    '4x12_price' => '4x12 avec prix',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
