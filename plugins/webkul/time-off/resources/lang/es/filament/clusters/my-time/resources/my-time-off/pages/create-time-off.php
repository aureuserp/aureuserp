<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Ausencia creada',
            'body'  => 'La ausencia se ha creado correctamente.',
        ],

        'overlap' => [
            'title' => 'Solicitud de ausencia superpuesta',
            'body'  => 'Las fechas de ausencia seleccionadas se superponen con una solicitud existente. Seleccionar fechas diferentes.',
        ],

        'warning' => [
            'title' => 'No tiene una cuenta de empleado',
            'body'  => 'No tiene una cuenta de empleado. Contactar con el administrador.',
        ],

        'invalid_half_day_leave' => [
            'title' => 'Solicitud de ausencia no válida',
            'body'  => 'La ausencia de medio día solo puede solicitarse para un único día.',
        ],

        'leave_request_denied_no_allocation' => [
            'title' => 'Solicitud de ausencia denegada',
            'body'  => 'No tiene ninguna ausencia asignada para :leaveType.',
        ],

        'leave_request_denied_insufficient_balance' => [
            'title' => 'Solicitud de ausencia denegada',
            'body'  => 'Saldo de ausencias insuficiente. Tiene :available_balance día(s) disponible(s). Solicitado: :requested_days día(s).',
        ],
    ],
];
