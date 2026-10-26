<?php

return [
    'navigation' => [
        'title' => 'Ajustes',
    ],

    'title' => 'Ajustes',

    'actions' => [
        'assign-now' => [
            'label'        => 'Asignar ahora',
            'description'  => 'Asigna ahora los leads sin asignar a equipos de ventas y comerciales, según las reglas de asignación.',
            'notification' => 'Se asignaron :teams lead(s) a equipos de ventas y :salespeople a comerciales.',
        ],
    ],

    'form' => [
        'lead-generation' => [
            'section'           => 'Generación de leads',
            'description'       => 'Cree leads a partir de los visitantes de su sitio web.',
            'visits-to-leads'   => 'Visitas a leads',
            'helper'            => 'Cree leads a partir de las visitas al sitio que coinciden con sus reglas de visitas a leads.',
            'no-rules'          => 'Aún no hay reglas activas: las visitas no crearán leads hasta que añada una.',
            'manage-rules'      => 'Gestionar reglas',
            'active-rules'      => ':count regla(s) activa(s).',
            'website-missing'   => 'Instale el complemento Sitio web para usar esta opción.',
            'generate-existing' => 'Crear leads para visitantes existentes',
            'generate-help'     => 'Aplica las reglas activas a las páginas ya visitadas por :count visitante(s) sin lead.',
            'generated'         => ':count lead(s) creado(s) a partir de visitantes existentes.',
        ],

        'scoring' => [
            'section'            => 'Puntuación predictiva de leads',
            'description'        => 'La probabilidad de ganar cada lead y oportunidad abiertos se calcula a partir de su historial de ganados y perdidos. La etapa y el equipo de ventas siempre se usan.',
            'variables'          => 'Considerar también',
            'variables-helper'   => 'Qué datos de un registro tiene en cuenta la probabilidad.',
            'start-date'         => 'Considerar leads creados a partir del',
            'start-date-helper'  => 'Déjelo vacío para usar todo el historial.',
            'update'             => 'Actualizar probabilidades',
            'update-description' => 'Guarda estas opciones, vuelve a aprender del historial de ganados y perdidos y recalcula todos los registros abiertos.',
            'updated'            => 'Probabilidades actualizadas para :count registro(s) abierto(s).',
        ],

        'sections' => [
            'crm' => 'CRM',
        ],

        'use-leads' => [

            'label'  => 'Leads',

            'helper' => 'Añadir un paso de calificación antes de crear una oportunidad.',

        ],

        'recurring-revenues' => [

            'label'  => 'Ingresos recurrentes',

            'helper' => 'Definir planes e ingresos recurrentes en las oportunidades.',

        ],

        'multi-teams' => [
            'label'  => 'Varios equipos',
            'helper' => 'Asignar comerciales a varios equipos de ventas.',
        ],

        'auto-assignment' => [
            'label'        => 'Asignación basada en reglas',
            'helper'       => 'Asigna leads periódicamente según reglas. Todos los equipos de ventas usan este ajuste por defecto, salvo que se indique lo contrario.',
            'running'      => 'Ejecución',
            'manual'       => 'Manualmente',
            'auto'         => 'Repetidamente',
            'repeat-every' => 'Repetir cada',
            'unit'         => 'Unidad',
            'next-run'     => 'Próxima ejecución: :date',

            'units' => [
                'minutes' => 'Minutos',
                'hours'   => 'Horas',
                'days'    => 'Días',
                'weeks'   => 'Semanas',
            ],
        ],
    ],
];
