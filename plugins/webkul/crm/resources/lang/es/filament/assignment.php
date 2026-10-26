<?php

return [
    'rules' => [
        'title'         => 'Reglas de asignación',
        'match'         => 'Coincidir',
        'match-all'     => 'Todas las reglas siguientes',
        'match-any'     => 'Cualquiera de las reglas siguientes',
        'conditions'    => 'Condiciones',
        'field'         => 'Campo',
        'operator'      => 'Operador',
        'value'         => 'Valor',
        'add-condition' => 'Añadir condición',
        'matching'      => '{1} :count registro abierto cumple estas reglas|[0,*] :count registros abiertos cumplen estas reglas',
        'no-conditions' => 'Sin condiciones: se acepta cualquier lead.',

        'fields' => [
            'type'                  => 'Tipo',
            'name'                  => 'Nombre',
            'contact_name'          => 'Nombre de contacto',
            'company_name'          => 'Nombre de la empresa',
            'job_title'             => 'Puesto de trabajo',
            'email'                 => 'Correo electrónico',
            'phone'                 => 'Teléfono',
            'website'               => 'Sitio web',
            'street1'               => 'Calle',
            'city'                  => 'Ciudad',
            'zip'                   => 'Código postal',
            'state_id'              => 'Estado',
            'country_id'            => 'País',
            'stage_id'              => 'Etapa',
            'tag_ids'               => 'Etiquetas',
            'priority'              => 'Prioridad',
            'probability'           => 'Probabilidad (%)',
            'expected_revenue'      => 'Ingreso esperado',
            'expected_closing_date' => 'Cierre previsto',
            'created_at'            => 'Creado el',
            'utm_campaign_id'       => 'Campaña',
            'utm_medium_id'         => 'Medio',
            'utm_source_id'         => 'Origen',
            'referred_by'           => 'Referido por',
        ],

        'operators' => [
            'contains'     => 'contiene',
            'not_contains' => 'no contiene',
            '='            => 'es igual a',
            '!='           => 'no es igual a',
            'starts_with'  => 'empieza por',
            'set'          => 'está definido',
            'not_set'      => 'no está definido',
            '>'            => 'mayor que',
            '>='           => 'mayor o igual que',
            '<'            => 'menor que',
            '<='           => 'menor o igual que',
            'in'           => 'está en',
            'not_in'       => 'no está en',

            'date' => [
                '>='      => 'es el o después del',
                '<='      => 'es el o antes del',
                'set'     => 'está definido',
                'not_set' => 'no está definido',
            ],
        ],
    ],

    'team' => [
        'company-helper'         => 'Empresas activadas en la barra superior. Para crear un equipo de otra empresa, cámbiese a ella primero.',
        'company-not-active'     => 'Esta empresa no está activa. Cámbiese a ella en la barra superior primero.',
        'section'                => 'Reglas de asignación',
        'section-description'    => 'Qué leads recibe este equipo con la asignación basada en reglas.',
        'capacity'               => ':assigned leads asignados en los últimos 30 días de un máximo de :max.',
        'use-leads'              => 'Leads',
        'use-opportunities'      => 'Oportunidades',
        'skip-assignment'        => 'Omitir asignación automática',
        'skip-assignment-helper' => 'Los leads no se asignan automáticamente a este equipo.',

        'assign-leads' => [
            'label'        => 'Asignar leads',
            'description'  => 'Asigna ahora los leads sin comercial de este equipo a sus miembros, según las reglas de asignación.',
            'notification' => 'Se asignaron :count lead(s) a comerciales.',
        ],
    ],

    'members' => [
        'new-salesperson' => [
            'heading' => 'Nuevo comercial',
        ],

        'title'       => 'Miembros',
        'description' => 'Los comerciales de este equipo, cuántos leads puede recibir cada uno en 30 días y qué leads aceptan.',
        'empty'       => 'Aún no hay miembros',

        'fields' => [
            'salesperson'       => 'Comercial',
            'capacity'          => 'Máx. leads (30 días)',
            'capacity-helper'   => 'Número máximo de leads asignados a este comercial en 30 días.',
            'capacity-assigned' => ':count asignados en los últimos 30 días.',
            'paused'            => 'Pausar asignación',
            'paused-helper'     => 'Este comercial no recibe nuevos leads automáticamente.',
            'active'            => 'Activo',
        ],

        'columns' => [
            'salesperson' => 'Comercial',
            'capacity'    => 'Máx. leads (30 días)',
            'last-month'  => 'Asignados (30 días)',
            'rules'       => 'Reglas',
            'paused'      => 'En pausa',
            'active'      => 'Activo',
        ],

        'actions' => [
            'add'    => 'Añadir miembro',
            'remove' => 'Quitar',
        ],

        'notifications' => [
            'added'        => 'Miembro añadido',
            'removed-from' => 'Quitado de: :teams (Varios equipos está desactivado).',

            'single-team' => [
                'title' => 'Un comercial solo puede estar en un equipo',
            ],
        ],
    ],
];
