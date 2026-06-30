<?php

return [
    'create-employee' => 'Crear empleado',
    'goto-employee'   => 'Ir al empleado',

    'notification' => [
        'title' => 'Candidato actualizado',
        'body'  => 'El candidato se ha actualizado correctamente.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Candidato eliminado',
                'body'  => 'El candidato se ha eliminado correctamente.',
            ],
        ],
        'force-delete' => [
            'notification' => [
                'title' => 'Candidato eliminado',
                'body'  => 'El candidato se ha eliminado de forma permanente correctamente.',
            ],
        ],

        'refuse' => [
            'title'        => 'Motivo de rechazo',
            'form'         => [
                'fields' => [
                    'notify'   => 'Notificar',
                    'email-to' => 'Enviar correo a',
                ],
            ],
            'notification' => [
                'title' => 'Candidato rechazado',
                'body'  => 'El candidato se ha rechazado correctamente.',
            ],
        ],

        'reopen' => [
            'title'        => 'Reabrir candidato',
            'notification' => [
                'title' => 'Candidato reabierto',
                'body'  => 'El candidato se ha reabierto correctamente.',
            ],
        ],

        'state' => [
            'notification' => [
                'title' => 'Estado del candidato actualizado',
                'body'  => 'El estado del candidato se ha actualizado correctamente.',
            ],
        ],
    ],

    'mail' => [
        'application-refused' => [
            'subject' => 'Tu candidatura: :application',
        ],

        'application-confirm' => [
            'subject' => 'Tu candidatura: :job_position',
        ],
        'interviewer-assigned' => [
            'subject' => 'Se te ha asignado al candidato :applicant.',
        ],
    ],
];
