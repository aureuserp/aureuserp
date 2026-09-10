<?php

return [
    'setup' => [
        'title'        => 'Envoyer un message',
        'submit-title' => 'Envoyer',

        'form' => [
            'fields' => [
                'hide-subject'            => 'Masquer le sujet',
                'add-subject'             => 'Ajouter un sujet',
                'subject'                 => 'Sujet',
                'write-message-here'      => 'Écrivez votre message ici',
                'attachments-helper-text' => 'Taille de fichier max : 10 Mo. Types autorisés : Images, PDF, Word, Excel, Texte',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Message envoyé',
                    'body'  => 'Votre message a été envoyé avec succès.',
                ],

                'error' => [
                    'title' => 'Erreur d\'envoi du message',
                    'body'  => 'Échec de l\'envoi de votre message',
                ],
            ],

            'mail' => [
                'subject' => ':record_name',
            ],
        ],
    ],
];
