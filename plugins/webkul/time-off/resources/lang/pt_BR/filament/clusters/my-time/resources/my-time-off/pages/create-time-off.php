<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Ausência criada',
            'body' => 'A ausência foi criada com sucesso.',
        ],

        'overlap' => [
            'title' => 'Solicitação de licença sobreposta',
            'body' => 'As datas de licença selecionadas se sobrepõem a uma solicitação existente. Escolha datas diferentes.',
        ],

        'warning' => [
            'title' => 'Você não tem uma conta de colaborador',
            'body' => 'Você não tem uma conta de colaborador. Entre em contato com seu administrador.',
        ],

        'invalid_half_day_leave' => [
            'title' => 'Solicitação de licença inválida',
            'body' => 'Licença de meio dia só pode ser solicitada para um único dia.',
        ],

        'leave_request_denied_no_allocation' => [
            'title' => 'Solicitação de licença negada',
            'body' => 'Você não tem nenhuma licença alocada para :leaveType.',
        ],

        'leave_request_denied_insufficient_balance' => [
            'title' => 'Solicitação de licença negada',
            'body' => 'Saldo de licença insuficiente. Você tem :available_balance dia(s) disponível(is). Solicitado: :requested_days dia(s).',
        ],
    ],
];
