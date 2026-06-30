<?php

return [
    'tooltip' => 'Filtros',

    'fields'  => [
        'search'             => 'Buscar',
        'search-placeholder' => 'Buscar mensajes...',
        'type'               => 'Tipo',
        'date'               => 'Fecha',
        'sort-by'            => 'Ordenar por',
        'pinned-only'        => 'Solo fijados',
    ],
    'type-options' => [
        'all'          => 'Todos los tipos',
        'note'         => 'Notas',
        'comment'      => 'Comentarios',
        'notification' => 'Notificaciones',
        'activity'     => 'Actividades',
    ],
    'date-options' => [
        ''          => 'Cualquier momento',
        'today'     => 'Hoy',
        'yesterday' => 'Ayer',
        'week'      => 'Últimos 7 días',
        'month'     => 'Últimos 30 días',
        'quarter'   => 'Últimos 3 meses',
        'year'      => 'Último año',
    ],
    'sort-options' => [
        'created_at_desc' => 'Más recientes primero',
        'created_at_asc'  => 'Más antiguos primero',
        'updated_at_desc' => 'Actualizados recientemente',
        'priority'        => 'Prioridad',
    ],
    'actions' => [
        'apply' => 'Aplicar filtros',
    ],
];
