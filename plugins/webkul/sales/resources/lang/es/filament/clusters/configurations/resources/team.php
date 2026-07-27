<?php

return [
    'title' => 'Equipos de ventas',

    'navigation' => [
        'title' => 'Equipos de ventas',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'     => 'Equipo de ventas',
                'status'   => 'Estado',
                'fieldset' => [
                    'team-details' => [
                        'title'  => 'Detalles del equipo',
                        'fields' => [
                            'team-leader'            => 'Líder del equipo',
                            'company'                => 'Empresa',
                            'invoiced-target'        => 'Objetivo facturado',
                            'invoiced-target-suffix' => '/ Mes',
                            'color'                  => 'Color',
                            'members'                => 'Miembros',
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
            'team-leader'     => 'Líder del equipo',
            'name'            => 'Nombre',
            'status'          => 'Estado',
            'invoiced-target' => 'Objetivo facturado',
            'color'           => 'Color',
            'created-by'      => 'Creado por',
            'created-at'      => 'Creado el',
            'updated-at'      => 'Actualizado el',
        ],

        'filters' => [
            'name'        => 'Nombre',
            'team-leader' => 'Líder del equipo',
            'company'     => 'Empresa',
            'created-by'  => 'Creado por',
            'updated-at'  => 'Actualizado el',
            'created-at'  => 'Creado el',
        ],

        'groups' => [
            'name'        => 'Nombre',
            'company'     => ' Empresa',
            'team-leader' => 'Líder del equipo',
            'created-at'  => 'Creado el',
            'updated-at'  => 'Actualizado el',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipo de ventas restaurado',
                    'body'  => 'El equipo de ventas se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipo de ventas eliminado',
                    'body'  => 'El equipo de ventas se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Equipo de ventas eliminado permanentemente',
                    'body'  => 'El equipo de ventas se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipos de ventas restaurados',
                    'body'  => 'Los equipos de ventas se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipos de ventas eliminados',
                    'body'  => 'Los equipos de ventas se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Equipos de ventas eliminados permanentemente',
                    'body'  => 'Los equipos de ventas se han eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Equipos de ventas creados',
                    'body'  => 'Los equipos de ventas se han creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'     => 'Equipo de ventas',
                'status'   => 'Estado',
                'fieldset' => [
                    'team-details' => [
                        'title'   => 'Detalles del equipo',
                        'entries' => [
                            'team-leader'            => 'Líder del equipo',
                            'company'                => 'Empresa',
                            'invoiced-target'        => 'Objetivo facturado',
                            'invoiced-target-suffix' => '/ Mes',
                            'color'                  => 'Color',
                            'members'                => 'Miembros',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
