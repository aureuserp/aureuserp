<?php

return [
    'stats' => [
        'open-sessions' => [
            'label'       => 'Sessions ouvertes',
            'description' => 'Sessions encore au comptoir',
        ],

        'orders' => [
            'label'       => 'Commandes',
            'description' => 'Commandes réglées sur la période sélectionnée',
        ],

        'revenue' => [
            'label'       => 'Chiffre d\'affaires',
            'description' => 'Total des commandes réglées sur la période sélectionnée',
        ],

        'failed-operations' => [
            'label'       => 'Opérations échouées',
            'description' => 'Commandes dont le mouvement de stock nécessite une attention',
        ],
    ],
];
