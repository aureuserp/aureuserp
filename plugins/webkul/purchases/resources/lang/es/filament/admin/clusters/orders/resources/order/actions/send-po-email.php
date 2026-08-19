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

            'warning' => [
                'title' => 'Algunos correos electrónicos no se enviaron',
                'body'  => 'Algunos proveedores no recibirán el correo electrónico porque su dirección de correo electrónico no está disponible.',
            ],

            'danger' => [
                'title' => 'Correo electrónico no enviado',
                'body'  => 'Agregue una dirección de correo electrónico a los proveedores seleccionados e inténtelo de nuevo.',
            ],
        ],
    ],
];
