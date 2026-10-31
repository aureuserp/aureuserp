<?php

return [
    'navigation' => [
        'label' => 'Pistes similaires',
    ],

    'page' => [
        'title' => 'Pistes similaires : :name',

        'empty' => [
            'heading'     => 'Aucun enregistrement similaire',
            'description' => 'Aucune autre piste ou opportunité n\'a le même e-mail, téléphone ou client.',
        ],
    ],

    'badges' => [
        'lost' => 'Perdue',
    ],

    'merge' => [
        'label'        => 'Fusionner',
        'heading'      => 'Fusionner les pistes et opportunités similaires',
        'submit'       => 'Fusionner',
        'description'  => 'Les champs vides de l\'enregistrement conservé sont remplis à partir des autres, et leurs notes, étiquettes, messages, activités et pièces jointes lui sont transférés. Les autres enregistrements sont ensuite supprimés. Le résultat est une opportunité si l\'un d\'eux en est une, et il est actif même si certains étaient perdus.',
        'chatter-note' => 'Fusionnés dans cet enregistrement : :records',

        'fields' => [
            'records'         => 'Enregistrements à fusionner',
            'head'            => 'Enregistrement à conserver',
            'head-helper'     => 'Ses informations sont conservées ; les champs vides sont remplis à partir des autres enregistrements.',
            'salesperson'     => 'Vendeur',
            'team'            => 'Équipe commerciale',
            'conflict-helper' => 'Les enregistrements sélectionnés ont des valeurs différentes ; choisissez celle que conservera l\'enregistrement fusionné.',
        ],

        'notifications' => [
            'invalid'     => 'Choisissez au moins deux enregistrements et celui à conserver.',
            'forbidden'   => 'Vous n\'êtes pas autorisé à fusionner ces enregistrements (il faut pouvoir modifier l\'enregistrement conservé et supprimer les autres).',
            'companies'   => 'Seuls les enregistrements de la même société peuvent être fusionnés.',
            'choose-user' => 'Choisissez le vendeur de l\'enregistrement fusionné.',
            'choose-team' => 'Choisissez l\'équipe commerciale de l\'enregistrement fusionné.',

            'merged' => [
                'title' => 'Enregistrements fusionnés',
                'body'  => ':count enregistrement(s) fusionné(s) dans « :name ».',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Piste',
            'type'        => 'Type',
            'contact'     => 'Nom du contact',
            'email'       => 'E-mail',
            'phone'       => 'Téléphone',
            'stage'       => 'Étape',
            'salesperson' => 'Vendeur',
            'team'        => 'Équipe commerciale',
            'created-at'  => 'Créé le',
        ],
    ],

    'graph' => [
        'heading' => 'Enregistrements par étape',
        'dataset' => 'Enregistrements',
    ],
];
