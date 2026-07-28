<?php

return [
    'notification' => [
        'title' => 'Alocação atualizada',
        'body' => 'A alocação foi atualizada com sucesso.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Alocação excluída',
                'body' => 'A alocação foi excluída com sucesso.',
            ],
        ],
        'approved' => [
            'title' => 'Aprovado',

            'notification' => [
                'title' => 'Alocação aprovada',
                'body' => 'A alocação foi aprovada com sucesso.',
            ],
        ],
        'refuse' => [
            'title' => 'Recusar',

            'notification' => [
                'title' => 'Alocação recusada',
                'body' => 'A alocação foi recusada com sucesso.',
            ],
        ],
        'mark-as-ready-to-confirm' => [
            'title' => 'Marcar como pronto para confirmar',

            'notification' => [
                'title' => 'Marcado como pronto para confirmar',
                'body' => 'A alocação foi marcada como pronta para confirmar com sucesso.',
            ],
        ],
    ],
];
