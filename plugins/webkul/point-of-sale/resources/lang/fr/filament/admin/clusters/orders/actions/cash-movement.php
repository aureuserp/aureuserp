<?php

return [
    'label'        => 'Entrée/Sortie d\'espèces',

    'form' => [
        'fields' => [
            'type'   => 'Type',
            'amount' => 'Montant',
            'reason' => 'Motif',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Mouvement d\'espèces enregistré',
            'body'  => 'Le solde du tiroir-caisse a été mis à jour.',
        ],
    ],
];
