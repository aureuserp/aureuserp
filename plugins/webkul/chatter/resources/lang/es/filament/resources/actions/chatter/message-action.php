<?php

return [
    'setup' => [
        'title'        => 'Enviar mensaje',
        'submit-title' => 'Enviar',

        'form' => [
            'fields' => [
                'hide-subject'            => 'Ocultar asunto',
                'add-subject'             => 'Añadir asunto',
                'subject'                 => 'Asunto',
                'write-message-here'      => 'Escriba su mensaje aquí',
                'attachments-helper-text' => 'Tamaño máximo de archivo: 10 MB. Tipos permitidos: imágenes, PDF, Word, Excel, texto',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Mensaje enviado',
                    'body'  => 'Su mensaje se ha enviado correctamente.',
                ],

                'error' => [
                    'title' => 'Error al enviar el mensaje',
                    'body'  => 'No se pudo enviar su mensaje',
                ],
            ],

            'mail' => [
                'subject' => ':record_name',
            ],
        ],
    ],
];
