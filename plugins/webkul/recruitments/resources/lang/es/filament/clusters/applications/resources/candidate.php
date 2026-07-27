<?php

return [
    'title' => 'Candidato',

    'navigation' => [
        'title' => 'Candidatos',
    ],

    'global-search' => [
        'email-from' => 'Correo de',
        'phone'      => 'Teléfono',
        'company'    => 'Empresa',
        'degree'     => 'Titulación',
    ],

    'form' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Información básica',

                'fields' => [
                    'full-name' => 'Nombre completo',
                    'email'     => 'Dirección de correo',
                    'phone'     => 'Número de teléfono',
                    'linkedin'  => 'Perfil de LinkedIn',
                    'contact'   => 'Contacto',
                ],
            ],

            'additional-details' => [
                'title' => 'Detalles adicionales',

                'fields' => [
                    'company'           => 'Empresa',
                    'degree'            => 'Titulación',
                    'tags'              => 'Etiquetas',
                    'manager'           => 'Responsable',
                    'availability-date' => 'Fecha de disponibilidad',

                    'priority-options' => [
                        'low'    => 'Baja',
                        'medium' => 'Media',
                        'high'   => 'Alta',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'Estado',

                'fields' => [
                    'active'     => 'Activo',
                    'evaluation' => 'Evaluación',
                ],
            ],

            'communication' => [
                'title' => 'Comunicación',

                'fields' => [
                    'cc-email'      => 'Correo CC',
                    'email-bounced' => 'Correo rebotado',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre completo',
            'tags'       => 'Etiquetas',
            'evaluation' => 'Evaluación',
        ],

        'filters' => [
            'company'      => 'Empresa',
            'partner-name' => 'Contacto',
            'degree'       => 'Titulación',
            'manager-name' => 'Responsable',
        ],

        'groups' => [
            'manager-name' => 'Responsable',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidato eliminado',
                    'body'  => 'Los candidatos se han eliminado correctamente.',
                ],
            ],

            'empty-state-actions' => [
                'create' => [
                    'notification' => [
                        'title' => 'Candidato creado',
                        'body'  => 'Los candidatos se han creado correctamente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidatos eliminados',
                    'body'  => 'Los candidatos se han eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Información básica',

                'entries' => [
                    'full-name' => 'Nombre completo',
                    'email'     => 'Dirección de correo',
                    'phone'     => 'Número de teléfono',
                    'linkedin'  => 'Perfil de LinkedIn',
                    'contact'   => 'Contacto',
                ],
            ],

            'additional-details' => [
                'title' => 'Detalles adicionales',

                'entries' => [
                    'company'           => 'Empresa',
                    'degree'            => 'Titulación',
                    'tags'              => 'Etiquetas',
                    'manager'           => 'Responsable',
                    'availability-date' => 'Fecha de disponibilidad',

                    'priority-options' => [
                        'low'    => 'Baja',
                        'medium' => 'Media',
                        'high'   => 'Alta',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'Estado',

                'entries' => [
                    'active'     => 'Activo',
                    'evaluation' => 'Evaluación',
                ],
            ],

            'communication' => [
                'title' => 'Comunicación',

                'entries' => [
                    'cc-email'      => 'Correo CC',
                    'email-bounced' => 'Correo rebotado',
                ],
            ],
        ],
    ],
];
