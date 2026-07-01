<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Condição de pagamento atualizada',
            'body' => 'A condição de pagamento foi atualizada com sucesso.',
        ],

        'validation-error' => [
            'title' => 'Erro de validação',
            'body' => 'O termo de vencimento deve ter pelo menos uma linha percentual e a soma dos percentuais deve ser 100%.',
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Condição de pagamento excluída',
                'body' => 'A condição de pagamento foi excluída com sucesso.',
            ],
        ],
    ],
];
