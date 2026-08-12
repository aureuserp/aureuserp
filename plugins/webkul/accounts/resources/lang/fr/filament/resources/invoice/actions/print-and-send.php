<?php

return [
    'title' => 'Imprimer et envoyer',

    'modal' => [
        'title' => 'Aperçu de la facture',

        'form' => [
            'partners'    => 'Client',
            'subject'     => 'Objet',
            'description' => 'Description',
            'files'       => 'Pièce jointe',
        ],

        'action' => [
            'submit' => [
                'title' => 'Envoyer',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Facture envoyée',
                'body'  => 'La facture a été envoyée avec succès.',
            ],
        ],
    ],
];
