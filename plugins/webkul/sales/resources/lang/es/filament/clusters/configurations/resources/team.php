<?php

return [
    'title' => 'Equipos de Ventas',

    'navigation' => [
        'title' => 'Equipos de Ventas',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'     => 'Equipo de Ventas',
                'status'   => 'Estado',
                'fieldset' => [
                    'team-details' => [
                        'title'  => 'Detalles del Equipo',
                        'fields' => [
                            'team-leader'           => 'Líder del Equipo',
                            'company'               => 'Empresa',
                            'invoiced-target'       => 'Objetivo de Facturación',
                            'invoiced-target-suffix' => '/ Mes',
                            'color'                 => 'Color',
                            'members'               => 'Miembros',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'company'         => 'Empresa',
            'team-leader'     => 'Líder del Equipo',
            'name'            => 'Nombre',
            'status'          => 'Estado',
            'invoiced-target' => 'Objetivo de Facturación',
            'color'           => 'Color',
            'created-by'      => 'Creado Por',
            'created-at'      => 'Creado En',
            'updated-at'      => 'Actualizado En',
        ],

        'filters' => [
            'name'        => 'Nombre',
            'team-leader' => 'Líder del Equipo',
            'company'     => 'Empresa',
            'created-by'  => 'Creado Por',
            'updated-at'  => 'Actualizado En',
            'created-at'  => 'Creado En',
        ],

        'groups' => [
            'name'        => 'Nombre',
            'company'     => ' Empresa',
            'team-leader' => 'Líder del Equipo',
            'created-at'  => 'Creado En',
            'updated-at'  => 'Actualizado En',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipo de Ventas restaurado',
                    'body'  => 'El equipo de ventas ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipo de Ventas eliminado',
                    'body'  => 'El equipo de ventas ha sido eliminado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Equipo de Ventas eliminado permanentemente',
                    'body'  => 'El equipo de ventas ha sido eliminado permanentemente exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipos de Ventas restaurados',
                    'body'  => 'Los equipos de ventas han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipos de Ventas eliminados',
                    'body'  => 'Los equipos de ventas han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Equipos de Ventas eliminados permanentemente',
                    'body'  => 'Los equipos de ventas han sido eliminados permanentemente exitosamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Equipos de Ventas creados',
                    'body'  => 'Los equipos de ventas han sido creados exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'     => 'Equipo de Ventas',
                'status'   => 'Estado',
                'fieldset' => [
                    'team-details' => [
                        'title'   => 'Detalles del Equipo',
                        'entries' => [
                            'team-leader'           => 'Líder del Equipo',
                            'company'               => 'Empresa',
                            'invoiced-target'       => 'Objetivo de Facturación',
                            'invoiced-target-suffix' => '/ Mes',
                            'color'                 => 'Color',
                            'members'               => 'Miembros',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
