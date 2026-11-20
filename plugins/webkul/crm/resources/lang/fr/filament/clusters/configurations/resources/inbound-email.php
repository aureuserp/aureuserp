<?php

return [
    'model-label'        => 'E-mail reçu',
    'plural-model-label' => 'E-mails reçus',

    'navigation' => [
        'title' => 'E-mails reçus',
    ],

    'table' => [
        'columns' => [
            'received-at' => 'Reçu',
            'from'        => 'De',
            'subject'     => 'Objet',
            'status'      => 'Résultat',
            'team'        => 'Équipe commerciale',
            'lead'        => 'Piste / Opportunité',
            'reason'      => 'Motif',
            'recipients'  => 'Destinataires',
            'server'      => 'Serveur de messagerie',
        ],

        'empty' => [
            'heading'     => 'Aucun e-mail reçu pour l\'instant',
            'description' => 'Les e-mails lus depuis les serveurs de messagerie entrants sont listés ici avec ce qui en a été fait.',
        ],
    ],
];
