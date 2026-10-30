<?php

return [
    'model-label'        => 'Client',
    'plural-model-label' => 'Clients',

    'navigation' => [
        'title' => 'Clients',
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
        'unauthorized' => 'Ce client ne peut pas être déplacé dans cette colonne.',
    ],

    'actions' => [
        'merge' => [
            'label'              => 'Fusionner',
            'heading'            => 'Fusionner les clients',
            'description'        => 'Tout ce qui est lié aux autres clients (opportunités, commandes, contacts enfants, messages…) passe au client conservé. Ses champs vides sont complétés à partir des autres, et les autres sont archivés.',
            'submit'             => 'Fusionner les clients',
            'destination'        => 'Client à conserver',
            'destination-helper' => 'Les autres clients sélectionnés sont fusionnés dans celui-ci.',
            'invalid'            => 'Sélectionnez au moins deux clients que vous pouvez modifier et archiver.',
            'notification'       => ':count client(s) fusionné(s) dans :name.',
            'log'                => 'Clients fusionnés : :names',
        ],

        'vcard' => [
            'label' => 'Télécharger (vCard)',
        ],

        'portal' => [
            'label'        => 'Accorder l\'accès au portail',
            'description'  => 'Chaque client sélectionné ayant une adresse e-mail reçoit une invitation à définir un mot de passe du portail.',
            'notification' => 'Accès au portail accordé à :granted client(s), :skipped ignoré(s) (sans e-mail, accès existant, e-mail déjà utilisé ou sans droit).',
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
