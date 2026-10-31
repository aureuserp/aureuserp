<?php

return [
    'title' => 'Passerelle SMS',

    'navigation' => [
        'group' => 'SMS',
    ],

    'actions' => [
        'test' => [
            'label'   => 'Envoyer un SMS de test',
            'number'  => 'Numéro de mobile',
            'message' => 'Votre passerelle SMS fonctionne.',
            'sent'    => 'SMS de test envoyé à :number.',
            'failed'  => 'Le SMS de test n\'a pas pu être envoyé.',
        ],
    ],

    'form' => [
        'active'           => 'Utiliser ces informations de passerelle',
        'active-helper'    => 'Les SMS envoyés depuis l\'ERP (par exemple « Envoyer un SMS » dans le CRM) passent par ce compte MSG91. Désactivé, les informations MSG91 de la configuration de l\'application sont utilisées.',
        'auth-key'         => 'Clé d\'authentification',
        'auth-key-helper'  => 'Laissez vide pour conserver la clé actuelle.',
        'sender-id'        => 'ID d\'expéditeur',
        'sender-id-helper' => 'Jusqu\'à 11 caractères, tel qu\'approuvé par votre fournisseur.',
        'route'            => 'Route',

        'routes' => [
            'transactional' => 'Transactionnel',
            'promotional'   => 'Promotionnel',
        ],
    ],
];
