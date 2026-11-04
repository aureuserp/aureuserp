<?php

return [
    'action'       => 'Configurer la comptabilité pour cette société',
    'notice'       => 'La comptabilité n\'est pas encore configurée pour cette société. Utilisez \'Configurer la comptabilité\' ci-dessous pour créer son plan comptable, ses journaux et ses paramètres par défaut.',
    'results'      => [
        'failed' => [
            'title' => 'La configuration comptable a échoué',
            'body'  => 'Une erreur inattendue a interrompu la configuration et rien n\'a été créé pour cette société. Réessayez ou contactez votre administrateur. Les détails techniques ont été enregistrés.',
        ],

        'no-company' => [
            'title' => 'Aucune société sélectionnée',
            'body'  => 'Sélectionnez la société pour laquelle configurer la comptabilité, puis réessayez.',
        ],
    ],
];
