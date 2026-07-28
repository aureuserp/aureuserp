<?php

return [
    'title' => 'Competencias',

    'navigation' => [
        'title' => 'Competencias',
    ],

    'form' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Detalles de la competencia',

                'fields' => [
                    'employee'       => 'Empleado',
                    'skill'          => 'Competencia',
                    'skill-level'    => 'Nivel',
                    'skill-type'     => 'Tipo de competencia',
                ],
            ],
            'addition-information' => [
                'title' => 'Información adicional',

                'fields' => [
                    'created-by' => 'Creado por',
                    'updated-by' => 'Actualizado por',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'employee'        => 'Empleado',
            'skill'           => 'Competencia',
            'skill-level'     => 'Nivel',
            'skill-type'      => 'Tipo de competencia',
            'user'            => 'Usuario',
            'proficiency'     => 'Dominio',
            'created-by'      => 'Creado por',
            'created-at'      => 'Creado el',
        ],

        'filters' => [
            'employee'        => 'Empleado',
            'skill'           => 'Competencia',
            'skill-level'     => 'Nivel',
            'skill-type'      => 'Tipo de competencia',
            'user'            => 'Usuario',
            'created-by'      => 'Creado por',
            'created-at'      => 'Creado el',
            'updated-at'      => 'Actualizado el',
        ],

        'groups' => [
            'employee'   => 'Empleado',
            'skill-type' => 'Tipo de competencia',
        ],
    ],

    'infolist' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Detalles de la competencia',

                'entries' => [
                    'employee'        => 'Empleado',
                    'skill'           => 'Competencia',
                    'skill-level'     => 'Nivel',
                    'skill-type'      => 'Tipo de competencia',
                ],
            ],

            'additional-information' => [
                'title' => 'Información adicional',

                'entries' => [
                    'created-by' => 'Creado por',
                    'updated-by' => 'Actualizado por',
                ],
            ],
        ],
    ],
];
