<?php

return [
    'create-employee' => 'Criar colaborador',
    'goto-employee' => 'Ir para colaborador',

    'notification' => [
        'title' => 'Candidato atualizado',
        'body' => 'O candidato foi atualizado com sucesso.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Candidato excluído',
                'body' => 'O candidato foi excluído com sucesso.',
            ],
        ],
        'force-delete' => [
            'notification' => [
                'title' => 'Candidato excluído',
                'body' => 'O candidato foi excluído permanentemente com sucesso.',
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
            'title' => 'Reabrir candidato',
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

        'application-confirm' => [
            'subject' => 'Sua candidatura: :job_position',
        ],
        'interviewer-assigned' => [
            'subject' => 'Você foi atribuído ao candidato :applicant.',
        ],
    ],
];
