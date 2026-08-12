<?php

return [
    'title' => 'Créer une facture',

    'modal' => [
        'heading' => 'Créer une facture',
    ],

    'notification' => [
        'invoice-created' => [
            'title' => 'Facture créée',
            'body'  => 'La facture a été créée avec succès.',
        ],

        'no-invoiceable-lines' => [
            'title' => 'Aucune ligne facturable',
            'body'  => 'Il n\'y a aucune ligne facturable, veuillez vous assurer qu\'une quantité a été reçue.',
        ],
    ],

    'form' => [
        'fields' => [
            'create-invoice' => 'Créer une facture',
        ],
    ],
];
