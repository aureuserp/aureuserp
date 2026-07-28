<?php

return [
    'title' => 'Ausência',

    'model-label' => 'Ausência',

    'navigation' => [
        'title' => 'Ausência',
    ],

    'global-search' => [
        'employee'      => 'Colaborador',
        'time-off-type' => 'Tipo de ausência',
        'date-from'     => 'Data inicial',
        'date-to'       => 'Data até',
    ],

    'form' => [
        'fields' => [
            'employee-name'     => 'Nome do colaborador',
            'department-name'   => 'Nome do departamento',
            'time-off-type'     => 'Tipo de ausência',
            'date'              => 'Data',
            'dates'             => 'Datas',
            'request-date-from' => 'Data inicial da solicitação',
            'request-date-to'   => 'Data final da solicitação',
            'description'       => 'Descrição',
            'period'            => 'Período',
            'half-day'          => 'Meio dia',
            'requested-days'    => 'Solicitado (dias/horas)',
            'description'       => 'Descrição',
            'attachment'        => 'Anexo',
            'day'               => ':day dia',
            'days'              => ':days dia(s)',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name' => 'Colaborador',
            'time-off-type' => 'Tipo de ausência',
            'description'   => 'Descrição',
            'date-from'     => 'Data inicial',
            'date-to'       => 'Data até',
            'duration'      => 'Duração',
            'status'        => 'Status',
        ],

        'groups' => [
            'employee-name' => 'Colaborador',
            'time-off-type' => 'Tipo de ausência',
            'status'        => 'Status',
            'start-date'    => 'Data de início',
            'start-to'      => 'Data de término',
            'updated-at'    => 'Atualizado em',
            'created-at'    => 'Criado em',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Validar',
                    'approve'  => 'Aprovar',
                ],
                'notification' => [
                    'title' => 'Ausência aprovada',
                    'body'  => 'A ausência foi aprovada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ausência excluída',
                    'body'  => 'A ausência foi excluída com sucesso.',
                ],
            ],

            'refused' => [
                'title'        => 'Recusar',
                'notification' => [
                    'title' => 'Ausência recusada',
                    'body'  => 'A ausência foi recusada com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ausência excluída',
                    'body'  => 'A ausência foi excluída com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'employee-name'     => 'Nome do colaborador',
            'department-name'   => 'Nome do departamento',
            'time-off-type'     => 'Tipo de ausência',
            'date'              => 'Data',
            'dates'             => 'Datas',
            'request-date-from' => 'Data inicial da solicitação',
            'request-date-to'   => 'Data final da solicitação',
            'description'       => 'Descrição',
            'period'            => 'Período',
            'half-day'          => 'Meio dia',
            'requested-days'    => 'Solicitado (dias/horas)',
            'attachment'        => 'Anexo',
            'day'               => ':day dia',
            'days'              => ':days dia(s)',
            'date-from'         => 'Data inicial',
            'date-to'           => 'Data até',
            'status'            => 'Status',
        ],
    ],
];
