<?php

return [
    'notification' => [
        'title' => 'Asignación actualizada',
        'body'  => 'La asignación se ha actualizado correctamente.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Asignación eliminada',
                'body'  => 'La asignación se ha eliminado correctamente.',
            ],
        ],
        'approved' => [
            'title' => 'Aprobado',

            'notification' => [
                'title' => 'Asignación aprobada',
                'body'  => 'La asignación se ha aprobado correctamente.',
            ],
        ],
        'refuse' => [
            'title' => 'Rechazar',

            'notification' => [
                'title' => 'Asignación rechazada',
                'body'  => 'La asignación se ha rechazado correctamente.',
            ],
        ],
        'mark-as-ready-to-confirm' => [
            'title' => 'Marcar como lista para confirmar',

            'notification' => [
                'title' => 'Marcada como lista para confirmar',
                'body'  => 'La asignación se ha marcado como lista para confirmar correctamente.',
            ],
        ],
    ],
];
