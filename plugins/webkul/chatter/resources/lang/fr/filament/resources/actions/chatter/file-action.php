<?php

return [
    'setup' => [
        'title'   => 'Pièces jointes',
        'tooltip' => 'Téléverser des pièces jointes',

        'modal-submit-action-label' => 'Téléverser',

        'form' => [
            'fields' => [
                'files'                  => 'Fichiers',
                'attachment-helper-text' => 'Taille de fichier max : 10 Mo. Types autorisés : Images, PDF, Word, Excel, Texte',

                'actions' => [
                    'delete' => [
                        'title' => 'Fichier supprimé',
                        'body'  => 'Le fichier a été supprimé avec succès.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Pièces jointes téléversées',
                    'body'  => 'Pièces jointes téléversées avec succès.',
                ],

                'warning'  => [
                    'title' => 'Aucun nouveau fichier',
                    'body'  => 'Tous les fichiers ont déjà été téléchargés.',
                ],

                'error' => [
                    'title' => 'Erreur de téléversement de pièce jointe',
                    'body'  => 'Échec du téléversement des pièces jointes ',
                ],
            ],
        ],
    ],
];
