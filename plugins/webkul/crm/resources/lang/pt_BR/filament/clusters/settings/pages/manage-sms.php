<?php

return [
    'title' => 'Gateway de SMS',

    'navigation' => [
        'group' => 'SMS',
    ],

    'actions' => [
        'test' => [
            'label'   => 'Enviar SMS de teste',
            'number'  => 'Número de celular',
            'message' => 'Seu gateway de SMS funciona.',
            'sent'    => 'SMS de teste enviado para :number.',
            'failed'  => 'Não foi possível enviar o SMS de teste.',
        ],
    ],

    'form' => [
        'active'           => 'Usar estes dados de gateway',
        'active-helper'    => 'Os SMS enviados pelo ERP (por exemplo "Enviar SMS" no CRM) passam por esta conta MSG91. Desativado, são usados os dados MSG91 da configuração da aplicação.',
        'auth-key'         => 'Chave de autenticação',
        'auth-key-helper'  => 'Deixe vazio para manter a chave atual.',
        'sender-id'        => 'ID do remetente',
        'sender-id-helper' => 'Até 11 caracteres, conforme aprovado pelo seu provedor.',
        'route'            => 'Rota',

        'routes' => [
            'transactional' => 'Transacional',
            'promotional'   => 'Promocional',
        ],
    ],
];
