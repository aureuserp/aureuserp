<?php

return [
    'title' => 'Equipos',

    'navigation' => [
        'title' => 'Equipos',
        'group' => 'Ajustes',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nombre',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nombre',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Equipo actualizado',
                    'body'  => 'El equipo ha sido actualizado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipo eliminado',
                    'body'  => 'El equipo ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Equipos creados',
                    'body'  => 'Los equipos han sido creados exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'          => 'Nombre',
                'job-title'     => 'Título del Puesto',
                'work-email'    => 'Correo Electrónico Laboral',
                'work-mobile'   => 'Móvil Laboral',
                'work-phone'    => 'Teléfono Laboral',
                'manager'       => 'Gerente',
                'department'    => 'Departamento',
                'job-position'  => 'Cargo',
                'team-tags'     => 'Etiquetas de Equipo',
                'coach'         => 'Entrenador',
            ],
        ],
    ],

    // El siguiente bloque 'infolist' está duplicado y solo contiene 'name'.
    // Se ha mantenido en la traducción, pero es posible que quieras revisar si es intencional.
    'infolist' => [
        'entries' => [
            'name' => 'Nombre',
        ],
    ],
];
