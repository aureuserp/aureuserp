<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Candidato excluído',
                'body' => 'O candidato foi excluído com sucesso.',
            ],
        ],

        'refuse' => [
            'title' => 'Motivo da recusa',
            'form' => [
                'fields' => [
                    'notify' => 'Notificar',
                    'email-to' => 'Enviar e-mail para',
                ],
            ],
            'notification' => [
                'title' => 'Candidato recusado',
                'body' => 'O candidato foi recusado com sucesso.',
            ],
        ],

        'reopen' => [
            'title' => 'Restaurar candidato recusado',
            'notification' => [
                'title' => 'Candidato reaberto',
                'body' => 'O candidato foi reaberto com sucesso.',
            ],
        ],

        'state' => [
            'notification' => [
                'title' => 'Estado do candidato atualizado',
                'body' => 'O estado do candidato foi atualizado com sucesso.',
            ],
        ],
    ],

    'mail' => [
        'application-refused' => [
            'subject' => 'Sua candidatura: :application',
        ],
    ],
];
