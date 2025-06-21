<?php

return [
    'label' => 'Enviar Orden de Compra por Email',

    'form' => [
        'fields' => [
            'to'      => 'Para',
            'subject' => 'Asunto',
            'message' => 'Mensaje',
        ],
    ],

    'action' => [
        'notification' => [
            'success' => [
                'title' => 'Email enviado',
                'body'  => 'El email ha sido enviado exitosamente.',
            ],
        ],
    ],
];
