<?php

return [
    'title'        => 'Enviar por correo electrónico',
    'resend-title' => 'Reenviar por correo electrónico',
    'quotation'    => 'presupuesto',
    'quotations'   => 'presupuestos',

    'modal' => [
        'heading' => 'Enviar presupuesto por correo electrónico',
    ],

    'form' => [
        'fields' => [
            'partners'    => 'Contactos',
            'subject'     => 'Asunto',
            'description' => 'Descripción',
            'attachment'  => 'Adjunto',
        ],
    ],

    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'Ningún destinatario seleccionado',
                    'body'  => 'Seleccione al menos un contacto al que enviar los presupuestos.',
                ],

                'all_success' => [
                    'title' => '¡Presupuestos enviados!',
                    'body'  => 'Sus :plural se han entregado correctamente a: :recipients',
                ],

                'all_failed' => [
                    'title' => 'No se pudieron enviar los presupuestos',
                    'body'  => 'Se produjeron problemas al enviar sus presupuestos: :failures',
                ],

                'partial_success' => [
                    'title'       => 'Algunos presupuestos enviados',
                    'sent_part'   => 'Entregado correctamente a: :recipients',
                    'failed_part' => 'No se pudo entregar a: :failures',
                ],

                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],

];
