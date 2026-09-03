<?php

return [
    'label'        => 'Envoyer par e-mail',
    'resend-label' => 'Renvoyer par e-mail',

    'form' => [
        'fields' => [
            'to'      => 'À',
            'subject' => 'Objet',
            'message' => 'Message',
        ],
    ],

    'action' => [
        'notification' => [
            'success' => [
                'title' => 'E-mail envoyé',
                'body'  => 'L\'e-mail a été envoyé avec succès.',
            ],

            'warning' => [
                'title' => 'Certains e-mails n\'ont pas été envoyés',
                'body'  => 'Certains fournisseurs ne recevront pas l\'e-mail car leur adresse e-mail n\'est pas disponible.',
            ],

            'danger' => [
                'title' => 'E-mail non envoyé',
                'body'  => 'Veuillez ajouter une adresse e-mail aux fournisseurs sélectionnés, puis réessayer.',
            ],
        ],
    ],
];
