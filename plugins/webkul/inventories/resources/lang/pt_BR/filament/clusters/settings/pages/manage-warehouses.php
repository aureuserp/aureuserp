<?php

return [
    'title' => 'Gerenciar armazéns',

    'form' => [
        'enable-locations' => 'Locais',
        'enable-locations-helper-text' => 'Rastreie a localização dos produtos no seu armazém',
        'configure-locations' => 'Configurar locais',
        'enable-multi-steps-routes' => 'Rotas em múltiplas etapas',
        'enable-multi-steps-routes-helper-text' => 'Use suas próprias rotas para gerenciar a transferência de produtos entre armazéns',
        'configure-routes' => 'Configurar rotas de armazém',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Ter vários armazéns',
                'body' => 'Você não pode desativar múltiplos locais se tiver mais de um armazém.',
            ],
        ],
    ],
];
