<?php

return [
    'title' => 'Imprimir y Enviar',

    'modal' => [
        'title' => 'Vista Previa de la Factura',

        'form' => [
            'partners'    => 'Cliente',
            'subject'     => 'Asunto',
            'description' => 'Descripción',
            'files'       => 'Adjunto',
        ],

        'action' => [
            'submit' => [
                'title' => 'Enviar',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Factura Enviada',
                'body'  => 'La factura ha sido enviada exitosamente.',
            ],
        ],
    ],
];
