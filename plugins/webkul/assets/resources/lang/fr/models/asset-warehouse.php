<?php

return [
    'title'        => 'Entrepôt d\'actifs',
    'deletion'     => [
        'referenced' => 'Impossible de supprimer un entrepôt référencé par des mouvements ou des actifs.',
    ],
    'deactivation' => [
        'has-assets' => 'Impossible de désactiver un entrepôt tant qu\'il contient des actifs stockés.',
    ],
    'company'      => [
        'immutable' => 'Impossible de modifier l\'entreprise d\'un entrepôt référencé par des mouvements ou des actifs.',
    ],
];
