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
        ],
    ],
];
