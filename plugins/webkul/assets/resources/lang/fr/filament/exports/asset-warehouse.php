<?php

return [
    'columns' => [
        'name'         => 'Nom',
        'code'         => 'Code',
        'custodian'    => 'Gestionnaire d\'entrepôt',
        'assets-count' => 'Actifs stockés',
        'total-cost'   => 'Évaluation totale',
        'is-active'    => 'Actif',
        'company'      => 'Entreprise',
        'creator'      => 'Créé par',
        'created-at'   => 'Créé le',
        'updated-at'   => 'Mis à jour le',
    ],

    'notification' => [
        'completed' => 'Votre export d\'entrepôts est terminé et :count ligne(s) exportée(s).',
        'failed'    => ':count ligne(s) n\'ont pas pu être exportée(s).',
    ],
];
