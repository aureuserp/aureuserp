<?php

return [
    'model-label'        => 'Lead',
    'plural-model-label' => 'Leads',

    'navigation' => [
        'title' => 'Leads',
    ],

    'global-search' => [
        'contact' => 'Contacto',
        'company' => 'Empresa',
    ],

    'form' => [
        'sections' => [
            'general' => 'Lead',
            'sales'   => 'Ventas',
        ],

        'tabs' => [
            'notes'      => 'Notas',
        ],

        'fieldsets' => [
            'marketing' => 'Marketing',
            'analysis'  => 'Análisis',
        ],

        'fields' => [
            'name-placeholder'    => 'p. ej. Precios de productos',
            'probability'         => 'Probabilidad',
            'company-name'        => 'Nombre de la empresa',
            'address'             => 'Dirección',
            'street1'             => 'Calle...',
            'street2'             => 'Calle 2...',
            'city'                => 'Ciudad',
            'zip'                 => 'Código postal',
            'state'               => 'Provincia',
            'country'             => 'País',
            'website'             => 'Sitio web',
            'website-placeholder' => 'p. ej. https://www.example.com',
            'salesperson'         => 'Comercial',
            'team'                => 'Equipo de ventas',
            'contact-name'        => 'Nombre del contacto',
            'email'               => 'Correo electrónico',
            'job-position'        => 'Cargo',
            'phone'               => 'Teléfono',
            'priority'            => 'Prioridad',
            'tags'                => 'Etiquetas',
            'company'             => 'Empresa',
            'campaign'            => 'Campaña',
            'medium'              => 'Medio',
            'source'              => 'Origen',
            'referred-by'         => 'Referido por',
            'assignment-date'     => 'Fecha de asignación',
            'closed-date'         => 'Fecha de cierre',
        ],
    ],

    'infolist' => [
        'sections' => [
            'lost' => 'Perdido',
        ],

        'entries' => [
            'lost-reason'  => 'Motivo de pérdida',
            'closing-note' => 'Nota de cierre',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Lead',
            'contact-name' => 'Nombre del contacto',
            'company-name' => 'Nombre de la empresa',
            'email'        => 'Correo electrónico',
            'phone'        => 'Teléfono',
            'city'         => 'Ciudad',
            'country'      => 'País',
            'salesperson'  => 'Comercial',
            'team'         => 'Equipo de ventas',
            'priority'     => 'Prioridad',
            'tags'         => 'Etiquetas',
            'source'       => 'Origen',
            'medium'       => 'Medio',
            'lost-reason'  => 'Motivo de pérdida',
            'created-at'   => 'Creado el',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'Nuevo lead',
        ],

        'open' => [
            'label' => 'Abrir',
        ],

        'schedule-activity' => [
            'label' => 'Programar actividad',
        ],

        'convert' => [
            'label' => 'Convertir en oportunidad',

            'modal' => [
                'heading' => 'Convertir en oportunidad',
                'submit'  => 'Crear oportunidad',
            ],

            'form' => [
                'action'     => 'Acción de conversión',
                'duplicates' => 'Leads y oportunidades similares a fusionar',

                'customer'          => 'Cliente',
                'existing-customer' => 'Cliente existente',
                'salesperson'       => 'Comercial',
                'team'              => 'Equipo de ventas',
            ],

            'notification' => [
                'title' => 'Convertido en oportunidad',
                'body'  => '":name" ahora es una oportunidad en el pipeline.',

                'invalid' => [
                    'title' => 'Selección no válida',
                    'body'  => 'El cliente, comercial o equipo de ventas seleccionado no pertenece a la empresa de este lead.',
                ],
            ],
        ],

        'mass-convert' => [
            'label' => 'Convertir en oportunidades',

            'modal' => [
                'heading' => 'Convertir en oportunidades',
                'submit'  => 'Convertir',
            ],

            'form' => [
                'deduplicate'             => 'Aplicar deduplicación',
                'deduplicate-helper'      => 'Fusionar cada lead con sus leads y oportunidades similares (mismo correo, teléfono o cliente).',
                'salespeople'             => 'Comerciales',
                'salespeople-helper'      => 'Los leads sin comercial se reparten entre ellos por turnos.',
                'force-assignment'        => 'Forzar asignación',
                'force-assignment-helper' => 'Reasignar también los leads que ya tienen comercial.',

                'customer-options' => [
                    'create' => 'Vincular a un cliente existente o crear uno',
                    'link'   => 'Vincular a un cliente existente',
                ],
            ],

            'notification' => [
                'title' => 'Leads convertidos',
                'body'  => ':count lead(s) convertido(s), :merged registro(s) similar(es) fusionado(s), :skipped omitido(s).',
            ],
        ],
        'lost' => [
            'notification' => [
                'title' => 'Lead perdido',
                'body'  => 'El lead se ha marcado como perdido y se ha movido a la vista Perdidos.',
            ],
        ],

        'restore' => [
            'notification' => [
                'title' => 'Lead restaurado',
                'body'  => 'El lead se ha restaurado.',
            ],
        ],

        'force-delete' => [
            'notification' => [
                'title' => 'Lead eliminado',
                'body'  => 'El lead se ha eliminado permanentemente.',
            ],
        ],
    ],
];
