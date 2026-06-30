<?php

return [
    'notification' => [
        'title'              => 'Ausencia actualizada',
        'body'               => 'La ausencia se ha actualizado correctamente.',
        'action_not_allowed' => [
            'title' => 'Acción no permitida',
            'body'  => 'No se puede modificar esta solicitud de ausencia porque se encuentra en un estado bloqueado.',
        ],
        'overlap' => [
            'title' => 'Solicitud de ausencia superpuesta',
            'body'  => 'Las fechas de ausencia seleccionadas se superponen con una solicitud existente. Seleccionar fechas diferentes.',
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Ausencia eliminada',
                'body'  => 'La ausencia se ha eliminado correctamente.',
            ],
        ],
    ],
];
