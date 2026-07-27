<?php

return [
    'label' => 'Enviar pedido de compra por correo electrónico',

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
                'title' => 'Correo electrónico enviado',
                'body'  => 'El correo electrónico se ha enviado correctamente.',
            ],
        ],
    ],
];
