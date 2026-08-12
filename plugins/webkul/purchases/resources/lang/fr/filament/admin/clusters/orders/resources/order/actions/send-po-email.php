<?php

return [
    'label' => 'Envoyer le bon de commande par e-mail',

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
