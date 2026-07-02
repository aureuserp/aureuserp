<?php

return [
    'form' => [
        'fields' => [
            'web'     => 'Web',
            'sanctum' => 'Sanctum',
            'select-all-permissions'      => 'Seleccionar todos los permisos',
            'select-all-permissions-hint' => 'Otorga todos los permisos a este rol. Desactívalo para borrar todo.',
        ],
    ],

    'notification' => [
        'system-role-delete' => [
            'title' => 'No se puede eliminar el rol del sistema',
            'body'  => 'Este es un rol del sistema y no se puede eliminar.',
        ],
    ],

    'matrix' => [
        'title'        => 'Permisos',
        'all-modules'  => 'Todos los complementos:',
        'select-all'   => 'Seleccionar todo',
        'deselect-all' => 'Deseleccionar todo',
        'search'       => 'Buscar complementos…',
        'model'        => 'Modelo',
        'action'       => 'Seleccionar acción',
        'all'          => 'todos',
        'none'         => 'ninguno',
        'granted'      => 'concedidos',
        'pages'        => 'Páginas',
        'widgets'      => 'Widgets',
        'empty'        => 'No hay permisos disponibles.',
    ],
];
