<?php

return [
    'form' => [
        'fieldsets' => [
            'account-properties' => [
                'label' => 'Propriétés du compte',

                'fields' => [
                    'income-account'                    => 'Compte de produits',
                    'income-account-hint-tooltip'       => 'Ce compte sera utilisé lors de la validation d\'une facture client.',
                    'expense-account'                   => 'Compte de charges',
                    'expense-account-hint-tooltip'      => 'La charge est enregistrée lors de la validation d\'une facture fournisseur, sauf en comptabilité anglo-saxonne avec valorisation en inventaire permanent, où elle est plutôt comptabilisée en tant que coût des marchandises vendues lors de la validation de la facture client.',
                    'down-payment-account'              => 'Compte d\'acompte',
                    'down-payment-account-hint-tooltip' => 'Sélectionnez le compte sur lequel les acomptes de cette catégorie seront enregistrés.',
                ],
            ],
        ],
    ],
];
