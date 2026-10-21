<?php

return [
    'title'      => 'Cajas',

    'navigation' => [
        'label' => 'Cajas',
    ],

    'session' => [
        'open'   => 'Abierta por :name',
        'closed' => 'Cerrada',
    ],

    'actions' => [
        'open'    => 'Abrir caja',
        'resume'  => 'Reanudar',

        'discard' => [
            'label'        => 'Descartar sesión',
            'heading'      => '¿Descartar esta sesión?',
            'description'  => 'La caja se abrió pero no se ha vendido nada en ella. Al descartarla se elimina la sesión para poder abrirla de nuevo sin residuos.',
            'confirm'      => 'Descartar',

            'notification' => [
                'title' => 'Sesión descartada',
            ],
        ],
    ],

    'empty' => [
        'heading'     => 'No hay ningún punto de venta disponible',
        'description' => 'Crea un punto de venta activo en el back office para empezar a vender.',
    ],
];
