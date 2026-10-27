<?php

return [
    'add-column' => [
        'stage'         => 'Añadir etapa',
        'activity-type' => 'Añadir tipo de actividad',
        'add'           => 'Añadir',
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

    'empty' => [
        'heading'           => 'Aún no hay nada aquí',
        'description'       => 'Arrastra una tarjeta aquí o crea una nueva.',
        'board-heading'     => 'No hay registros para mostrar',
        'board-description' => 'Nada coincide con esta vista o con los filtros actuales.',
    ],

    'progress' => [
        'label' => 'Actividades de esta etapa',
        'total' => 'Ingreso esperado',
    ],
];
