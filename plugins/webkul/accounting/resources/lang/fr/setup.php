<?php

return [
    'action'       => 'Configurer la comptabilité pour cette société',
    'notice'       => 'La comptabilité n\'est pas encore configurée pour cette société. Utilisez \'Configurer la comptabilité\' ci-dessous pour créer son plan comptable, ses journaux et ses paramètres par défaut.',
    'notification' => [
        'title' => 'Comptabilité configurée',
        'body'  => 'Le plan comptable, les journaux et les paramètres par défaut ont été créés pour cette société.',
    ],
    'error' => [
        'title' => 'La configuration comptable n\'a pas été exécutée',
        'body'  => 'Aucun plan comptable, journal ou paramètre par défaut n\'a été créé. Vérifiez que la société par défaut utilisée comme modèle est entièrement configurée, puis réessayez. Les détails techniques ont été enregistrés.',
    ],
];
