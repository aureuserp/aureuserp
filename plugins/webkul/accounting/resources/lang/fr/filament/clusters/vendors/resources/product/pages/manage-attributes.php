<?php

return [
    'title' => 'Attributs',

    'form' => [
        'attribute' => 'Attribut',
        'values'    => 'Valeurs',
    ],

    'table' => [
        'description' => 'Attention : l\'ajout ou la suppression d\'attributs supprimera et recréera les variantes existantes, entraînant la perte de leurs personnalisations éventuelles.',

        'header-actions' => [
            'create' => [
                'label' => 'Ajouter un attribut',

                'notification' => [
                    'title' => 'Attribut créé',
                    'body'  => 'L\'attribut a été créé avec succès.',
                ],
            ],
        ],

        'columns' => [
            'attribute' => 'Attribut',
            'values'    => 'Valeurs',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Attribut mis à jour',
                    'body'  => 'L\'attribut a été mis à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Attribut supprimé',
                    'body'  => 'L\'attribut a été supprimé avec succès.',
                ],
            ],
        ],
    ],
];
