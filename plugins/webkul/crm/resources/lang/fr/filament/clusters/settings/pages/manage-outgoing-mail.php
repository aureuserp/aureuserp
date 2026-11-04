<?php

return [
    'title' => 'Serveur de courrier sortant',

    'actions' => [
        'test' => [
            'label'   => 'Envoyer un e-mail de test',
            'subject' => 'E-mail de test',
            'body'    => '<p>Votre serveur de courrier sortant fonctionne.</p>',
            'sent'    => 'E-mail de test envoyé à :email.',
            'failed'  => 'L\'e-mail de test n\'a pas pu être envoyé.',
        ],
    ],

    'form' => [
        'active'              => 'Utiliser un serveur de courrier sortant personnalisé',
        'active-helper'       => 'Les e-mails envoyés depuis l\'ERP (par exemple « Envoyer un e-mail » du CRM) passent par ce serveur. Désactivé, le serveur de la configuration de l\'application est utilisé.',
        'host'                => 'Serveur SMTP',
        'encryption'          => 'Chiffrement',
        'port'                => 'Port',
        'username'            => 'Nom d\'utilisateur',
        'password'            => 'Mot de passe',
        'password-helper'     => 'Laissez vide pour conserver le mot de passe actuel.',
        'from-address'        => 'Adresse d\'expédition',
        'from-address-helper' => 'Adresse depuis laquelle les e-mails sont envoyés. Vide = celle de l\'application.',
        'from-name'           => 'Nom d\'expéditeur',

        'encryptions' => [
            'tls'  => 'STARTTLS (port 587)',
            'ssl'  => 'SSL/TLS (port 465)',
            'none' => 'Aucun (port 25)',
        ],
    ],
];
