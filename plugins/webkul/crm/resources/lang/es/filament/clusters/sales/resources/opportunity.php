<?php

return [
    'model-label'        => 'Oportunidad',
    'plural-model-label' => 'Oportunidades',

    'navigation' => [
        'title' => 'Mi pipeline',
    ],

    'global-search' => [
        'customer' => 'Cliente',
        'stage'    => 'Etapa',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Oportunidad',

                'fields' => [
                    'name'             => 'Oportunidad',
                    'name-placeholder' => 'p. ej. Precios de productos',
                    'expected-revenue' => 'Ingresos esperados',
                    'probability'      => 'Probabilidad',
                    'customer'         => 'Cliente',
                    'email'            => 'Correo electrónico',
                    'phone'            => 'Teléfono',
                ],
            ],

            'notes' => [
                'title' => 'Notas internas',
            ],

            'sales' => [
                'title' => 'Ventas',

                'fields' => [
                    'salesperson'      => 'Comercial',
                    'team'             => 'Equipo de ventas',
                    'company'          => 'Empresa',
                    'expected-closing' => 'Cierre previsto',
                    'priority'         => 'Prioridad',
                    'tags'             => 'Etiquetas',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'lost' => [
                'title' => 'Perdida',

                'entries' => [
                    'lost-reason' => 'Motivo de pérdida',
                    'feedback'    => 'Nota de cierre',
                ],
            ],

            'general' => [
                'title' => 'Oportunidad',

                'entries' => [
                    'expected-revenue' => 'Ingresos esperados',
                    'probability'      => 'Probabilidad',
                    'customer'         => 'Cliente',
                    'email'            => 'Correo electrónico',
                    'phone'            => 'Teléfono',
                ],
            ],

            'notes' => [
                'title' => 'Notas internas',
            ],

            'sales' => [
                'title' => 'Ventas',

                'entries' => [
                    'salesperson'      => 'Comercial',
                    'team'             => 'Equipo de ventas',
                    'company'          => 'Empresa',
                    'expected-closing' => 'Cierre previsto',
                    'priority'         => 'Prioridad',
                    'tags'             => 'Etiquetas',
                    'closed-at'        => 'Cerrada el',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Oportunidad',
            'customer'         => 'Cliente',
            'stage'            => 'Etapa',
            'expected-revenue' => 'Ingresos esperados',
            'probability'      => 'Probabilidad',
            'priority'         => 'Prioridad',
            'salesperson'      => 'Comercial',
            'team'             => 'Equipo de ventas',
            'expected-closing' => 'Cierre previsto',
            'tags'             => 'Etiquetas',
            'email'            => 'Correo electrónico',
            'phone'            => 'Teléfono',
            'lost-reason'      => 'Motivo de pérdida',
            'created-at'       => 'Creada el',
        ],

        'summaries' => [
            'total' => 'Total',
        ],

        'groups' => [
            'stage'            => 'Etapa',
            'team'             => 'Equipo de ventas',
            'salesperson'      => 'Comercial',
            'priority'         => 'Prioridad',
            'expected-closing' => 'Cierre previsto',
        ],

        'filters' => [
            'stage'       => 'Etapa',
            'team'        => 'Equipo de ventas',
            'salesperson' => 'Comercial',
            'tags'        => 'Etiquetas',
            'priority'    => 'Prioridad',
        ],

        'actions' => [
            'schedule-activity' => [
                'label' => 'Programar actividad',
            ],

            'restore' => [
                'label' => 'Restaurar',

                'notification' => [
                    'title' => 'Oportunidad restaurada',
                    'body'  => 'La oportunidad se ha restaurado en el pipeline.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Oportunidad eliminada',
                    'body'  => 'La oportunidad se ha eliminado permanentemente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'label' => 'Restaurar',

                'notification' => [
                    'title' => 'Oportunidades restauradas',
                    'body'  => 'Las oportunidades se han restaurado en el pipeline.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Oportunidades eliminadas',
                    'body'  => 'Las oportunidades se han eliminado permanentemente.',
                ],
            ],
        ],
    ],

    'actions' => [
        'new-quotation' => [
            'label'             => 'Nueva cotización',
            'modal-heading'     => 'Nueva cotización',
            'modal-description' => 'Esta oportunidad aún no tiene cliente. Elija el cliente de la cotización.',
            'submit'            => 'Continuar',
            'subheading'        => 'Desde la oportunidad: :name',
        ],

        'open-opportunity' => [
            'label' => 'Oportunidad',
        ],

        'won' => [
            'label' => 'Ganada',

            'modal' => [
                'heading'     => 'Marcar como ganada',
                'description' => 'La oportunidad se moverá a la etapa ganada con una probabilidad del 100%.',
            ],

            'notification' => [
                'success' => [
                    'title' => 'Oportunidad ganada',
                    'body'  => 'La oportunidad se ha marcado como ganada.',
                ],

                'no-won-stage' => [
                    'title' => 'No hay etapa ganada',
                    'body'  => 'Primero marque una etapa como "¿Es etapa ganada?" en CRM → Configuración → Etapas.',
                ],
            ],
        ],

        'lost' => [
            'label' => 'Perdida',

            'modal' => [
                'heading' => 'Marcar como perdida',
                'submit'  => 'Marcar como perdida',
            ],

            'form' => [
                'lost-reason' => 'Motivo de pérdida',
                'feedback'    => 'Nota de cierre',
            ],

            'notification' => [
                'title' => 'Oportunidad perdida',
                'body'  => 'La oportunidad se ha marcado como perdida y se ha movido a la vista Perdidas.',
            ],
        ],
    ],

    'quotations' => [
        'title' => 'Cotizaciones',

        'columns' => [
            'number'      => 'Número',
            'date'        => 'Fecha',
            'customer'    => 'Cliente',
            'salesperson' => 'Comercial',
            'total'       => 'Total',
            'status'      => 'Estado',
        ],

        'empty' => [
            'heading'     => 'Aún no hay cotizaciones',
            'description' => 'Use "Nueva cotización" para crear una cotización desde esta oportunidad.',
        ],
    ],
];
