<?php

return [
    'move' => [
        'no-journal-found-title' => 'La comptabilité n\'est pas configurée',
        'no-journal-found'       => 'Aucun journal n\'a été trouvé dans la société :company pour l\'un des types suivants : :types. Veuillez d\'abord configurer un plan comptable pour cette société.',
    ],

    'posting' => [
        'invalid-entry'         => 'L\'écriture comptable n\'est pas valide : :field.',
        'unbalanced'            => 'L\'écriture comptable :move n\'est pas équilibrée : débit :debit, crédit :credit.',
        'rejected-by-validator' => 'La comptabilisation a été refusée : :reason',
        'move-not-postable'     => 'L\'écriture comptable ne peut pas être comptabilisée : :reason',
        'move-not-reversible'   => 'L\'écriture comptable :move ne peut pas être extournée.',
        'duplicate-source'      => 'La clé source a déjà été utilisée par l\'écriture comptable :move avec des lignes différentes.',
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
