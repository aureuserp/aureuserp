<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'        => 'Título',
                'name'         => 'Nombre',
                'type'         => 'Tipo',
                'create-type'  => 'Crear tipo',
                'duration'     => 'Duración',
                'start-date'   => 'Fecha de inicio',
                'end-date'     => 'Fecha de fin',
                'display-type' => 'Tipo de visualización',
                'description'  => 'Descripción',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Título',
            'start-date'   => 'Fecha de inicio',
            'end-date'     => 'Fecha de fin',
            'display-type' => 'Tipo de visualización',
            'description'  => 'Descripción',
            'created-by'   => 'Creado por',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'groups' => [
            'group-by-type'         => 'Agrupar por tipo',
            'group-by-display-type' => 'Agrupar por tipo de visualización',
        ],

        'header-actions' => [
            'add-resume' => 'Añadir currículum',
        ],

        'filters' => [
            'type'            => 'Tipo',
            'start-date-from' => 'Fecha de inicio desde',
            'start-date-to'   => 'Fecha de inicio hasta',
            'created-from'    => 'Creado desde',
            'created-to'      => 'Creado hasta',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Currículum actualizado',
                    'body'  => 'El currículum se ha actualizado correctamente.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Currículum creado',
                    'body'  => 'El currículum se ha creado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Currículum eliminado',
                    'body'  => 'El currículum se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Currículums eliminados',
                    'body'  => 'Los currículums se han eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title'        => 'Título',
            'display-type' => 'Tipo de visualización',
            'type'         => 'Tipo',
            'description'  => 'Descripción',
            'duration'     => 'Duración',
            'start-date'   => 'Fecha de inicio',
            'end-date'     => 'Fecha de fin',
        ],
    ],
];
