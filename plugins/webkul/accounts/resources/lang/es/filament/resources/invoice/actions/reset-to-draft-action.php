<?php

return [
    'title' => 'Restablecer a borrador',

    'validation' => [
        'notification' => [
            'error' => [
                'invalid-state' => [
                    'title' => 'Estado del asiento contable no válido',
                    'body'  => 'Solo los asientos contables contabilizados o cancelados pueden restablecerse a borrador.',
                ],
            ],
        ],
    ],
];
