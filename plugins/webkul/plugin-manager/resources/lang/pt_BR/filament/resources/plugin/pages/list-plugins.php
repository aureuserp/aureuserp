<?php

return [
    'navigation' => [
        'title' => 'Plugins',
    ],

    'tabs' => [
        'apps' => 'Apps',
        'extra' => 'Extra',
        'installed' => 'Instalado',
        'not-installed' => 'Não instalado',
    ],

    'header-actions' => [
        'sync' => [
            'label' => 'Sincronizar plugins disponíveis',
            'modal-heading' => 'Sincronizar plugins',
            'modal-description' => 'Isso irá verificar e registrar todos os novos plugins encontrados.',
            'modal-submit-action-label' => 'Sincronizar plugins',

            'notification' => [
                'success' => [
                    'title' => 'Plugins sincronizados com sucesso',
                    'body' => ':count novo(s) plugin(s) encontrado(s) e sincronizado(s).',
                ],

                'error' => [
                    'title' => 'Falha na sincronização de plugins',
                    'body' => 'Ocorreu um erro (:error) ao sincronizar os plugins. Tente novamente.',
                ],
            ],
        ],
    ],
];
