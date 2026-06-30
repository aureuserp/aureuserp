<?php

return [
    'title' => 'Equipos',

    'navigation' => [
        'title' => 'Equipos',
        'group' => 'Configuración',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nombre',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'created-by' => 'Creado por',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Equipo actualizado',
                    'body'  => 'El equipo se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipo eliminado',
                    'body'  => 'El equipo se ha eliminado correctamente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Equipos creados',
                    'body'  => 'Los equipos se han creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'          => 'Nombre',
                'job-title'     => 'Puesto de trabajo',
                'work-email'    => 'Correo electrónico laboral',
                'work-mobile'   => 'Móvil laboral',
                'work-phone'    => 'Teléfono laboral',
                'manager'       => 'Gerente',
                'department'    => 'Departamento',
                'job-position'  => 'Cargo',
                'team-tags'     => 'Etiquetas de equipo',
                'coach'         => 'Mentor',
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nombre',
        ],
    ],
];
