<?php

return [
    'add-column' => [
        'stage'         => 'Añadir etapa',
        'activity-type' => 'Añadir tipo de actividad',
        'placeholder'   => 'Nombre…',
        'add'           => 'Añadir',
        'cancel'        => 'Cancelar',
        'created'       => '":name" añadido',
        'exists'        => '":name" ya existe',
    ],

    'column-actions' => [
        'settings'           => 'Ajustes: :name',
        'edit'               => 'Editar',
        'delete'             => 'Eliminar',
        'name'               => 'Nombre',
        'edit-heading'       => 'Editar ":name"',
        'delete-heading'     => '¿Eliminar ":name"?',
        'delete-description' => 'Esta acción no se puede deshacer.',
        'saved'              => '":name" guardado',
        'deleted'            => '":name" eliminado',

        'in-use' => [
            'title' => 'No se puede eliminar ":name"',
            'body'  => 'Todavía tiene :count registro. Muévelo primero a otra columna.|Todavía tiene :count registros. Muévelos primero a otra columna.',
        ],
    ],

    'progress' => [
        'label'   => 'Actividades de esta etapa',
        'planned' => 'Actividades planificadas',
        'today'   => 'Actividades para hoy',
        'overdue' => 'Actividades vencidas',
        'none'    => 'Sin actividades',
        'total'   => 'Ingreso esperado',
    ],
];
