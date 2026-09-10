    <?php

    return [
        'setup' => [
            'title'               => 'Abonnés',
            'submit-action-title' => 'Ajouter un abonné',
            'tooltip'             => 'Ajouter un abonné',

            'form' => [
                'fields' => [
                    'recipients'  => 'Destinataires',
                    'notify-user' => 'Notifier l\'utilisateur',
                    'add-a-note'  => 'Ajouter une note',
                ],
            ],

            'actions' => [
                'notification' => [
                    'success' => [
                        'title' => 'Abonné ajouté',
                        'body'  => 'L\'abonné a été ajouté avec succès.',
                    ],

                    'partial_message' => [
                        'title'    => 'Message envoyé avec un avis',
                        'single'   => ':count abonné n\'a pas été notifié en raison d\'un e-mail manquant : :names',
                        'multiple' => ':count abonnés n\'ont pas été notifiés en raison d\'e-mails manquants : :names',
                    ],

                    'error' => [
                        'title' => 'Erreur d\'ajout d\'abonné',
                        'body'  => 'Échec de l\'ajout de ":partner" comme abonné',
                    ],
                ],

                'mail' => [
                    'subject' => 'Invitation à suivre :model : :department',
                ],
            ],
        ],
    ];
