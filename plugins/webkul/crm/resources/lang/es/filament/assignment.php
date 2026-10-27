<?php

return [
    'rules' => [
        'title'         => 'Reglas de asignación',
        'conditions'    => 'Condiciones',
        'field'         => 'Campo',
        'operator'      => 'Operador',
        'value'         => 'Valor',
        'add-condition' => 'Nueva regla',

        'heading-all' => 'Cumplir todas las reglas siguientes:',
        'heading-any' => 'Cumplir cualquiera de las reglas siguientes:',
        'records'     => '{0} Ningún registro coincide|{1} :count registro|[2,*] :count registros',
        'breakdown'   => ':leads leads · :opportunities oportunidades',

        'records-modal' => [
            'heading'     => 'Registros coincidentes',
            'description' => 'Leads y oportunidades abiertos que aún no están asignados y cumplen estas reglas. Se excluyen los que ya están en un equipo (o, para un miembro, los que ya tienen comercial).',
            'empty'       => 'Ningún lead u oportunidad abierto cumple estas reglas.',
            'showing'     => 'Mostrando los :shown más recientes de :total registros.',
            'close'       => 'Cerrar',

            'columns' => [
                'name'             => 'Nombre',
                'type'             => 'Tipo',
                'contact'          => 'Contacto',
                'email'            => 'Correo',
                'salesperson'      => 'Comercial',
                'expected-revenue' => 'Ingreso esperado',
                'stage'            => 'Etapa',
            ],
        ],

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
    ],

    'team' => [
        'company-helper'         => 'Empresas activadas en la barra superior. Para crear un equipo de otra empresa, cámbiese a ella primero.',
        'company-not-active'     => 'Esta empresa no está activa. Cámbiese a ella en la barra superior primero.',
        'section'                => 'Reglas de asignación',
        'section-description'    => 'Qué leads recibe este equipo con la asignación basada en reglas.',
        'capacity'               => ':assigned leads asignados este mes de un máximo de :max',
        'use-leads'              => 'Leads',
        'use-opportunities'      => 'Oportunidades',
        'skip-assignment'        => 'Omitir asignación automática',
        'skip-assignment-helper' => 'Los leads no se asignan automáticamente a este equipo.',

        'assign-leads' => [
            'label'          => 'Asignar leads',
            'heading'        => 'Asignar leads',
            'description'    => 'Elige los leads a repartir. Cada uno va al miembro del equipo con más espacio libre.',
            'submit'         => 'Asignar',
            'records'        => 'Leads a asignar',
            'empty'          => 'Selecciona al menos un lead.',
            'skipped'        => 'No se pudieron repartir :count (sin espacio libre o las reglas del miembro no coinciden).',
            'capacity'       => 'Espacio libre: :members',
            'no-members'     => 'Ningún miembro del equipo puede recibir leads ahora.',
            'nothing'        => 'No hay leads para asignar ahora.',
            'notification'   => 'Se asignaron :count lead(s) a comerciales.',
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
            'removed-from' => 'Quitado de: :teams (Vendedores en varios equipos está desactivado).',

            'single-team' => [
                'title' => 'Un comercial solo puede estar en un equipo',
            ],
        ],
    ],
];
