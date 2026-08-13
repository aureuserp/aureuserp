<?php

return [
    'title' => 'Annuler',
    'modal' => [
        'heading'     => 'Annuler le devis',
        'description' => 'Êtes-vous sûr de vouloir annuler ce devis ?',
    ],

    'footer-actions' => [
        'send-and-cancel' => [
            'title' => 'Envoyer et annuler',

            'notification' => [
                'cancelled' => [
                    'title' => 'Devis annulé',
                    'body'  => 'Le devis a été annulé et l\'e-mail a été envoyé avec succès.',
                ],
            ],
        ],

        'cancel' => [
            'title' => 'Annuler',

            'notification' => [
                'cancelled' => [
                    'title' => 'Devis annulé',
                    'body'  => 'Le devis a été annulé avec succès.',
                ],
            ],
        ],

        'close' => [
            'title' => 'Fermer',
        ],
    ],

    'form' => [
        'fields' => [
            'partner'             => 'Partenaire',
            'subject'             => 'Objet',
            'subject-placeholder' => 'Objet',
            'subject-default'     => 'Le devis :name a été annulé pour la commande client #:id',
            'description'         => 'Description',
            'description-default' => 'Cher/Chère <b>:partner_name</b>, <br/><br/>Nous souhaitons vous informer que votre commande client <b>:name</b> a été annulée. Par conséquent, aucun frais supplémentaire ne sera appliqué à cette commande. Si un remboursement est nécessaire, il sera traité dans les meilleurs délais.<br/><br/>Pour toute question ou assistance supplémentaire, n\'hésitez pas à nous contacter.',
        ],
    ],
];
