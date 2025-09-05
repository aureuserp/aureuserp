<?php

return [
    'title' => 'Habilidades',

    'navigation' => [
        'title' => 'Habilidades',
    ],

    'form' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Detalles de la Habilidad',

                'fields' => [
                    'employee'   => 'Empleado',
                    'skill'      => 'Habilidad',
                    'skill-level' => 'Nivel',
                    'skill-type' => 'Tipo de Habilidad',
                ],
            ],
            'addition-information' => [
                'title' => 'Información Adicional',

                'fields' => [
                    'created-by' => 'Creado Por',
                    'updated-by' => 'Actualizado Por',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'          => 'ID',
            'employee'    => 'Empleado',
            'skill'       => 'Habilidad',
            'skill-level' => 'Nivel',
            'skill-type'  => 'Tipo de Habilidad',
            'user'        => 'Usuario',
            'proficiency' => 'Dominio',
            'created-by'  => 'Creado Por',
            'created-at'  => 'Creado El',
            'updated-at'  => 'Actualizado El',
        ],

        'filters' => [
            'employee'    => 'Empleado',
            'skill'       => 'Habilidad',
            'skill-level' => 'Nivel',
            'skill-type'  => 'Tipo de Habilidad',
            'user'        => 'Usuario',
            'created-by'  => 'Creado Por',
            'created-at'  => 'Creado El',
            'updated-at'  => 'Actualizado El',
        ],

        'groups' => [
            'employee'   => 'Empleado',
            'skill-type' => 'Tipo de Habilidad',
        ],
    ],

    'infolist' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Detalles de la Habilidad',

                'entries' => [
                    'employee'    => 'Empleado',
                    'skill'       => 'Habilidad',
                    'skill-level' => 'Nivel',
                    'skill-type'  => 'Tipo de Habilidad',
                ],
            ],

            'additional-information' => [
                'title' => 'Información Adicional',

                'entries' => [
                    'created-by' => 'Creado Por',
                    'updated-by' => 'Actualizado Por',
                ],
            ],
        ],
    ],
];
