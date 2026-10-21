<?php

return [
    'title' => 'Comptabilité',

    'form' => [
        'fields' => [
            'receivable-account'      => 'Compte client par défaut',
            'stock-output-account'    => 'Compte de sortie de stock',
            'balancing-account'       => 'Compte d\'équilibrage par défaut',
            'enable-cogs'             => 'Comptabiliser le coût des ventes',
            'enable-cogs-helper-text' => 'Comptabilise une écriture de coût à partir de l\'instantané du coût produit. Il n\'y a pas encore de couche de valorisation, le compte de sortie de stock se comporte donc comme une contrepartie du coût des ventes.',
            'allow-balancing-line'    => 'Autoriser la ligne d\'équilibrage',
        ],
    ],
];
