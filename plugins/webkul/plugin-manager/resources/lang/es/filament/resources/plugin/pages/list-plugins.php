<?php

return [
    'navigation' => [
        'title' => 'Plugins',
    ],

    'tabs' => [
        'apps'          => 'Aplicaciones',
        'extra'         => 'Extra',
        'installed'     => 'Instalados',
        'not-installed' => 'No instalados',
    ],

    'header-actions' => [
        'sync' => [
            'label'                     => 'Sincronizar plugins disponibles',
            'modal-heading'             => 'Sincronizar plugins',
            'modal-description'         => 'Esto buscará y registrará cualquier plugin nuevo encontrado.',
            'modal-submit-action-label' => 'Sincronizar plugins',

            'notification' => [
                'success' => [
                    'title' => 'Plugins sincronizados correctamente',
                    'body'  => 'Se encontraron y sincronizaron :count plugin(s) nuevo(s).',
                ],

                'error' => [
                    'title' => 'Error al sincronizar plugins',
                    'body'  => 'Se produjo un error (:error) al sincronizar los plugins. Vuelva a intentarlo.',
                ],
            ],
        ],
    ],
];
