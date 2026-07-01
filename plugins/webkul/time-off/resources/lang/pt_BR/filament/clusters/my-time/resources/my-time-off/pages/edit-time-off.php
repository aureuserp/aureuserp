<?php

return [
    'notification' => [
        'title' => 'Ausência atualizada',
        'body' => 'A ausência foi atualizada com sucesso.',
        'action_not_allowed' => [
            'title' => 'Ação não permitida',
            'body' => 'Você não pode modificar esta solicitação de licença porque ela está em um estado bloqueado.',
        ],
        'overlap' => [
            'title' => 'Solicitação de licença sobreposta',
            'body' => 'As datas de licença selecionadas se sobrepõem a uma solicitação existente. Escolha datas diferentes.',
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Ausência excluída',
                'body' => 'A ausência foi excluída com sucesso.',
            ],
        ],
    ],
];
