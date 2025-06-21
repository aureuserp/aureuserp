<?php

return [
    'title' => 'Cancelar',
    'modal' => [
        'heading'     => 'Cancelar Cotización',
        'description' => '¿Estás seguro de que deseas cancelar esta Cotización?',
    ],

    'footer-actions' => [
        'send-and-cancel' => [
            'title' => 'Enviar y Cancelar',

            'notification' => [
                'cancelled' => [
                    'title' => 'Cotización cancelada',
                    'body'  => 'La cotización ha sido cancelada y el correo electrónico ha sido enviado exitosamente.',
                ],
            ],
        ],

        'cancel' => [
            'title' => 'Cancelar',

            'notification' => [
                'cancelled' => [
                    'title' => 'Cotización cancelada',
                    'body'  => 'La cotización ha sido cancelada exitosamente.',
                ],
            ],
        ],

        'close' => [
            'title' => 'Cerrar',
        ],
    ],

    'form' => [
        'fields' => [
            'partner'             => 'Socio',
            'subject'             => 'Asunto',
            'subject-placeholder' => 'Asunto',
            'subject-default'     => 'La Cotización :name ha sido cancelada para la Orden de Venta #:id',
            'description'         => 'Descripción',
            'description-default' => 'Estimado/a <b>:partner_name</b>, <br/><br/>Le informamos que su Orden de Venta <b>:name</b> ha sido cancelada. Como resultado, no se aplicarán más cargos a este pedido. Si se requiere un reembolso, se procesará a la mayor brevedad.<br/><br/>Si tiene alguna pregunta o necesita más ayuda, no dude en comunicarse con nosotros.',
        ],
    ],
];
