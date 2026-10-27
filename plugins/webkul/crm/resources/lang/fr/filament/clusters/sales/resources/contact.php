<?php

return [
    'model-label'        => 'Contact',
    'plural-model-label' => 'Contacts',

    'navigation' => [
        'title' => 'Contacts',
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nom',
            'phone'       => 'Téléphone',
            'email'       => 'E-mail',
            'salesperson' => 'Vendeur',
            'city'        => 'Ville',
            'country'     => 'Pays',
            'company'     => 'Société',
        ],
    ],

    'grid' => [
        'opportunities' => '{1} :count opportunité|[0,*] :count opportunités',
        'sale-orders'   => '{1} :count commande client|[0,*] :count commandes client',
    ],

    'board' => [
        'unauthorized' => 'Ce contact ne peut pas être déplacé dans cette colonne.',
    ],

    'actions' => [
        'merge' => [
            'label'              => 'Fusionner',
            'heading'            => 'Fusionner les contacts',
            'description'        => 'Tout ce qui est lié aux autres contacts (opportunités, commandes, contacts enfants, messages…) passe au contact conservé. Ses champs vides sont complétés à partir des autres, et les autres sont archivés.',
            'submit'             => 'Fusionner les contacts',
            'destination'        => 'Contact à conserver',
            'destination-helper' => 'Les autres contacts sélectionnés sont fusionnés dans celui-ci.',
            'invalid'            => 'Sélectionnez au moins deux contacts que vous pouvez modifier et archiver.',
            'notification'       => ':count contact(s) fusionné(s) dans :name.',
            'log'                => 'Contacts fusionnés : :names',
        ],

        'vcard' => [
            'label' => 'Télécharger (vCard)',
        ],

        'portal' => [
            'label'        => 'Accorder l\'accès au portail',
            'description'  => 'Chaque contact sélectionné ayant une adresse e-mail reçoit une invitation à définir un mot de passe du portail.',
            'notification' => 'Accès au portail accordé à :granted contact(s), :skipped ignoré(s) (sans e-mail, accès existant, e-mail déjà utilisé ou sans droit).',
        ],
    ],

    'infolist' => [
        'sections' => [
            'overview' => 'Aperçu',
        ],

        'fields' => [
            'opportunities' => 'Opportunités',
            'sale-orders'   => 'Commandes client',
        ],
    ],
];
