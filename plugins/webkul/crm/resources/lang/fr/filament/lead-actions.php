<?php

return [
    'mark-lost' => [
        'label'        => 'Marquer comme perdu',
        'notification' => ':count enregistrement(s) marqué(s) comme perdu(s).',
    ],

    'send-email' => [
        'label'        => 'Envoyer un e-mail',
        'short-label'  => 'E-mail',
        'submit'       => 'Envoyer',

        'form' => [
            'recipient' => 'À',
            'subject'   => 'Objet',
            'body'      => 'Message',
        ],

        'sent'         => 'E-mail envoyé à :email.',
        'failed'       => 'L\'e-mail n\'a pas pu être envoyé.',
        'notification' => ':sent e-mail(s) envoyé(s), :skipped enregistrement(s) ignoré(s) (sans adresse e-mail ou sans accès).',
    ],

    'send-sms' => [
        'label'        => 'Envoyer un SMS',
        'short-label'  => 'SMS',
        'submit'       => 'Envoyer le SMS',

        'form' => [
            'recipient' => 'Destinataire',
            'message'   => 'Message',
        ],

        'log-subject'  => 'SMS à :number',
        'sent'         => 'SMS envoyé à :number.',
        'failed'       => 'Le SMS n\'a pas pu être envoyé.',
        'notification' => ':sent SMS envoyé(s), :skipped enregistrement(s) ignoré(s) (sans numéro valide ou sans accès).',
    ],
];
