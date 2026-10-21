<?php

return [
    'model-label'        => 'Etapa',
    'plural-model-label' => 'Etapas',

    'navigation' => [
        'title' => 'Etapas',
        'group' => 'Pipeline',
    ],

    'form' => [
        'fields' => [
            'name'                     => 'Nombre de la etapa',
            'is-won'                   => '¿Es etapa ganada?',
            'is-won-helper-text'       => 'Las oportunidades movidas a esta etapa se marcan como ganadas (probabilidad del 100%).',
            'requirements'             => 'Requisitos',
            'requirements-helper-text' => 'Indicaciones internas sobre lo que debe hacerse antes de mover una oportunidad a esta etapa.',
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'Nombre de la etapa',
            'is-won'        => 'Etapa ganada',
            'requirements'  => 'Requisitos',
            'opportunities' => 'Oportunidades',
            'created-at'    => 'Creado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etapa actualizada',
                    'body'  => 'La etapa se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etapa eliminada',
                    'body'  => 'La etapa se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etapas eliminadas',
                    'body'  => 'Las etapas se han eliminado correctamente.',
                ],
            ],
        ],
    ],
];
