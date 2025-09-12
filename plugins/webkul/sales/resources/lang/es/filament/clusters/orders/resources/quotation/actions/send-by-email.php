<?php

return [
    'title' => 'Enviar por Correo Electrónico',

    'modal' => [
        'heading' => 'Enviar Cotización por Correo Electrónico',
    ],

    'form' => [
        'fields' => [
            'partners'    => 'Socios',
            'subject'     => 'Asunto',
            'description' => 'Descripción',
            'attachment'  => 'Adjunto',
        ],
    ],

    'actions' => [
        'notification' => [
            'title' => 'Cotización enviada',
            'body'  => 'La cotización ha sido enviada exitosamente.',
        ],
    ],
];
