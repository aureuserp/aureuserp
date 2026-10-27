<?php

return [
    'alias' => [
        'section'             => 'Alias e-mail',
        'section-description' => 'Les e-mails envoyés à cette adresse créent des pistes dans cette équipe.',
        'name'                => 'Alias e-mail',
        'name-helper'         => 'La partie avant le @, par ex. « ventes ».',
        'name-invalid'        => 'Utilisez uniquement des lettres, chiffres, points, tirets ou tirets bas.',
        'name-taken'          => 'Cet alias est déjà utilisé par une autre équipe commerciale.',
        'domain'              => 'Domaine de l\'alias',
        'domain-helper'       => 'La partie après le @, par ex. « votreentreprise.com ». La boîte d\'un serveur de messagerie entrant doit recevoir les e-mails de ce domaine.',
        'domain-invalid'      => 'Saisissez un domaine comme votreentreprise.com.',
        'contact'             => 'Accepter les e-mails de',
    ],

    'reasons' => [
        'no-sender'   => 'L\'e-mail n\'a pas d\'adresse d\'expéditeur valide.',
        'auto-reply'  => 'Réponse automatique (absence, rebond ou liste de diffusion).',
        'from-alias'  => 'Envoyé depuis l\'un des alias des équipes commerciales (boucle de messagerie).',
        'no-alias'    => 'Envoyé à aucun alias d\'équipe commerciale.',
        'not-allowed' => 'L\'alias n\'autorise pas cet expéditeur (« Accepter les e-mails de » : :contact).',
    ],

    'errors' => [
        'no-imap'    => 'L\'extension PHP imap n\'est pas installée sur le serveur.',
        'connection' => 'Impossible de se connecter au serveur de messagerie.',
    ],

    'chatter' => [
        'from' => 'De :',
    ],

    'no-subject' => 'E-mail de :email',
];
