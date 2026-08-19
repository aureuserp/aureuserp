<?php

return [
    'move' => [
        'no-journal-found-title' => 'La comptabilité n\'est pas configurée',
        'no-journal-found'       => 'Aucun journal n\'a été trouvé dans la société :company pour l\'un des types suivants : :types. Veuillez d\'abord configurer un plan comptable pour cette société.',
    ],

    'tax-formula' => [
        'empty'                => 'La formule ne peut pas être vide.',
        'invalid-character'    => 'La formule contient un caractère invalide : :character',
        'unexpected-token'     => 'La formule contient un jeton inattendu : :token',
        'unexpected-end'       => 'La formule se termine de manière inattendue.',
        'unclosed-parenthesis' => 'La formule contient une parenthèse non fermée.',
        'unknown-variable'     => 'Variable inconnue « :variable ». Seules ces variables sont disponibles : :variables',
        'unknown-function'     => 'Fonction inconnue « :function ». Seules ces fonctions sont disponibles : :functions',
    ],
];
