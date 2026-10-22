<?php

return [
    'model-label'        => 'Nivel',
    'plural-model-label' => 'Niveles',

    'navigation' => [
        'title' => 'Niveles',
        'group' => 'Miembros',
    ],

    'header-actions' => [
        'create' => 'Nuevo nivel',
    ],

    'form' => [
        'name'          => 'Nombre',
        'weight'        => 'Peso del nivel',
        'weight-helper' => 'Probabilidad de que los socios de este nivel reciban leads. 0 significa ninguno.',
    ],

    'table' => [
        'columns' => [
            'name'     => 'Nombre',
            'weight'   => 'Peso del nivel',
            'partners' => 'Contactos',
        ],
    ],

    'actions' => [
        'archive' => 'Archivar',
    ],

    'tabs' => [
        'active'   => 'Activos',
        'archived' => 'Archivados',
    ],

    'notifications' => [
        'created'  => 'Nivel creado',
        'updated'  => 'Nivel actualizado',
        'archived' => 'Nivel archivado',
        'restored' => 'Nivel restaurado',
        'deleted'  => 'Nivel eliminado',
    ],

    'contact' => [
        'section'       => 'Nivel de socio',
        'level'         => 'Nivel',
        'weight'        => 'Peso del nivel',
        'weight-helper' => 'Déjelo vacío para usar el peso del nivel.',
    ],
];
