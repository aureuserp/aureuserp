<?php

return [
    'title'        => 'Envoyer par e-mail',
    'resend-title' => 'Renvoyer par e-mail',
    'quotation'    => 'devis',
    'quotations'   => 'devis',

    'modal' => [
        'heading' => 'Envoyer le devis par e-mail',
    ],

    'form' => [
        'fields' => [
            'partners'    => 'Partenaires',
            'subject'     => 'Objet',
            'description' => 'Description',
            'attachment'  => 'Pièce jointe',
        ],
    ],

    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'Aucun destinataire sélectionné',
                    'body'  => 'Veuillez sélectionner au moins un partenaire pour envoyer les devis.',
                ],

                'all_success' => [
                    'title' => 'Devis envoyés !',
                    'body'  => 'Vos :plural ont été livrés avec succès à : :recipients',
                ],

                'all_failed' => [
                    'title' => 'Impossible d\'envoyer les devis',
                    'body'  => 'Nous avons rencontré des problèmes lors de l\'envoi de vos devis : :failures',
                ],

                'partial_success' => [
                    'title'       => 'Certains devis envoyés',
                    'sent_part'   => 'Livrés avec succès à : :recipients',
                    'failed_part' => 'Impossible de livrer à : :failures',
                ],

                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],

];
