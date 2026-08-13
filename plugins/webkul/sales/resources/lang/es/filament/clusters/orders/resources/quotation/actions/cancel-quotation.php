<?php

return [
    'title' => 'Cancelar',
    'modal' => [
        'heading'     => 'Cancelar presupuesto',
        'description' => '¿Seguro que quiere cancelar este presupuesto?',
    ],

    'footer-actions' => [
        'send-and-cancel' => [
            'title' => 'Enviar y cancelar',

            'notification' => [
                'cancelled' => [
                    'title' => 'Presupuesto cancelado',
                    'body'  => 'El presupuesto se ha cancelado y el correo se ha enviado correctamente.',
                ],
            ],
        ],

        'cancel' => [
            'title' => 'Cancelar',

            'notification' => [
                'cancelled' => [
                    'title' => 'Presupuesto cancelado',
                    'body'  => 'El presupuesto se ha cancelado correctamente.',
                ],
            ],
        ],

        'close' => [
            'title' => 'Cerrar',
        ],
    ],

    'form' => [
        'fields' => [
            'partner'             => 'Contacto',
            'subject'             => 'Asunto',
            'subject-placeholder' => 'Asunto',
            'subject-default'     => 'El presupuesto :name se ha cancelado para el pedido de venta #:id',
            'description'         => 'Descripción',
            'description-default' => 'Estimado/a <b>:partner_name</b>, <br/><br/>Le informamos que su pedido de venta <b>:name</b> ha sido cancelado. Como resultado, no se aplicarán más cargos a este pedido. Si se requiere un reembolso, se procesará lo antes posible.<br/><br/>Si tiene alguna pregunta o necesita más ayuda, no dude en ponerse en contacto con nosotros.',
        ],
    ],
];
