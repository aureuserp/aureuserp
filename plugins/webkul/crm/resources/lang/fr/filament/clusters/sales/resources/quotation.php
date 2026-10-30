<?php

return [
    'navigation' => [
        'title' => 'Mes devis',
    ],

    'pages' => [
        'list' => [
            'title' => 'Mes devis',
        ],
    ],

    'tabs' => [
        'opportunity' => 'Opportunité',
    ],

    'board' => [
        'unauthorized' => 'Vous ne pouvez pas déplacer ce devis dans cette colonne.',

        'card' => [
            'open'        => 'Ouvrir :name',
            'customer'    => 'Client',
            'total'       => 'Total',
            'date'        => 'Date de commande',
            'salesperson' => 'Vendeur',
        ],
    ],

    'actions' => [
        'mark-as-sent' => [
            'label'        => 'Marquer le devis comme envoyé',
            'notification' => ':count devis marqué(s) comme envoyé(s).',
        ],

        'send-email' => [
            'label'        => 'Envoyer un e-mail',
            'description'  => 'Chaque devis sélectionné est envoyé par e-mail à son client avec le PDF en pièce jointe. Les brouillons sont marqués comme envoyés.',
            'submit'       => 'Envoyer',
            'subject'      => 'Devis :customer (Réf :name)',
            'body'         => 'Bonjour :customer,<br><br>Votre devis <strong>:name</strong> d\'un montant de <strong>:amount</strong> est prêt à être consulté.<br><br>N\'hésitez pas à nous contacter pour toute question.',
            'notification' => ':sent devis envoyé(s), :skipped ignoré(s) (pas un devis, pas d\'e-mail client ou pas d\'accès).',
        ],

        'confirm' => [
            'label'        => 'Confirmer',
            'description'  => 'Les devis sélectionnés deviennent des bons de commande.',
            'notification' => ':confirmed devis confirmé(s), :skipped ignoré(s).',
        ],
    ],

    'team-filter' => [
        'label' => 'Équipe commerciale : :team',
        'clear' => 'Tout afficher',
    ],
];
