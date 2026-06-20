<?php

return [
    'navigation' => [
        'title' => 'Recrutamento',
        'group' => 'Dashboard',
    ],

    'filters-form' => [
        'job-position' => 'Cargo',
        'departments' => 'Departamentos',
        'companies' => 'Empresas',
        'stages' => 'Estágios',
        'status'       => [
            'title' => 'Status',
            'options' => [
                'all' => 'Todos',
                'ongoing' => 'Em andamento',
                'hired' => 'Contratado',
                'refused' => 'Recusado',
                'archived' => 'Arquivados',
            ],
        ],

        'start-date' => 'Data de início',
        'end-date' => 'Data de término',
    ],
];
