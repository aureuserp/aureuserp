<?php

return [
    'title' => 'Imprimir y enviar',

    'modal' => [
        'title' => 'Vista previa de la factura',

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
                'title' => 'Factura enviada',
                'body'  => 'La factura se ha enviado correctamente.',
            ],
        ],
    ],
];
