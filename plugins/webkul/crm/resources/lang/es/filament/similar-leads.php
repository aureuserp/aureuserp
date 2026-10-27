<?php

return [
    'navigation' => [
        'label' => 'Leads similares',
    ],

    'page' => [
        'title' => 'Leads similares: :name',

        'empty' => [
            'heading'     => 'No hay registros similares',
            'description' => 'Ningún otro lead u oportunidad tiene el mismo correo, teléfono o cliente.',
        ],

        'views' => [
            'kanban' => 'Kanban',
            'list'   => 'Lista',
            'graph'  => 'Gráfico',
        ],
    ],

    'badges' => [
        'lost' => 'Perdido',
    ],

    'merge' => [
        'label'        => 'Fusionar',
        'heading'      => 'Fusionar leads y oportunidades similares',
        'submit'       => 'Fusionar',
        'description'  => 'Los campos vacíos del registro que se conserva se completan con los de los demás, y sus notas, etiquetas, mensajes, actividades y adjuntos pasan a él. Después se eliminan los demás registros. El resultado es una oportunidad si alguno lo es, y queda activo aunque alguno estuviera perdido.',
        'chatter-note' => 'Fusionados en este registro: :records',

        'fields' => [
            'records'         => 'Registros a fusionar',
            'head'            => 'Registro a conservar',
            'head-helper'     => 'Se conservan sus datos; los campos vacíos se completan con los demás registros.',
            'salesperson'     => 'Comercial',
            'team'            => 'Equipo de ventas',
            'conflict-helper' => 'Los registros seleccionados tienen valores distintos; elige el que conservará el registro fusionado.',
        ],

        'notifications' => [
            'invalid'     => 'Elige al menos dos registros y el que se conserva.',
            'forbidden'   => 'No tienes permiso para fusionar estos registros (necesitas editar el registro conservado y eliminar permanentemente los demás).',
            'companies'   => 'Solo se pueden fusionar registros de la misma empresa.',
            'choose-user' => 'Elige el comercial del registro fusionado.',
            'choose-team' => 'Elige el equipo de ventas del registro fusionado.',

            'merged' => [
                'title' => 'Registros fusionados',
                'body'  => 'Se fusionaron :count registro(s) en ":name".',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Lead',
            'type'        => 'Tipo',
            'contact'     => 'Nombre del contacto',
            'email'       => 'Correo electrónico',
            'phone'       => 'Teléfono',
            'stage'       => 'Etapa',
            'salesperson' => 'Comercial',
            'team'        => 'Equipo de ventas',
            'created-at'  => 'Creado el',
        ],
    ],

    'graph' => [
        'heading' => 'Registros por etapa',
        'dataset' => 'Registros',
    ],
];
