<?php

return [
    'navigation' => [
        'title' => 'Plugins',
    ],

    'tabs' => [
        'apps'          => 'Applications',
        'extra'         => 'Supplémentaires',
        'installed'     => 'Installés',
        'not-installed' => 'Non installés',
    ],

    'header-actions' => [
        'sync' => [
            'label'                     => 'Synchroniser les plugins disponibles',
            'modal-heading'             => 'Synchroniser les plugins',
            'modal-description'         => 'Ceci analysera et enregistrera tout nouveau plugin trouvé.',
            'modal-submit-action-label' => 'Synchroniser les plugins',

            'notification' => [
                'success' => [
                    'title' => 'Plugins synchronisés avec succès',
                    'body'  => ':count nouveau(x) plugin(s) trouvé(s) et synchronisé(s).',
                ],

                'error' => [
                    'title' => 'Échec de la synchronisation des plugins',
                    'body'  => 'Une erreur (:error) est survenue lors de la synchronisation des plugins. Veuillez réessayer.',
                ],
            ],
        ],
    ],
];
