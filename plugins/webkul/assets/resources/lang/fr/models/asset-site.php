<?php

return [
    'title' => 'Site d\'actifs et de gardes',

    'deletion' => [
        'referenced' => 'Impossible de supprimer un site référencé par des enregistrements de garde ou des actifs.',
    ],

    'deactivation' => [
        'open-custody' => 'Impossible de désactiver un site tant qu\'il détient des enregistrements de garde ouverts.',
    ],

    'company' => [
        'immutable' => 'Impossible de modifier la société d\'un site référencé par des enregistrements de garde ou des actifs.',
    ],

    'hierarchy' => [
        'circular-reference' => 'Un site ne peut pas être défini comme enfant de lui-même ou de l\'un de ses descendants.',
    ],

    'usage' => [
        'has-fixed-assets'   => 'Impossible de changer le type d\'utilisation du site en zone de garde tant que des actifs fixes sont déployés sans employé dans celui-ci ou ses sous-sites.',
        'has-custody-assets' => 'Impossible de changer le type d\'utilisation du site en emplacement d\'actifs tant que des actifs en garde d\'employés sont actifs dans celui-ci ou ses sous-sites.',
    ],
];
