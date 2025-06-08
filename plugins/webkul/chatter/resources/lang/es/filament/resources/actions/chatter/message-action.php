<?php

return [
    'setup' => [
        'title'        => 'Enviar Mensaje',
        'submit-title' => 'Enviar',

        'form' => [
            'fields' => [
                'hide-subject'            => 'Ocultar Asunto',
                'add-subject'             => 'Añadir Asunto',
                'subject'                 => 'Asunto',
                'write-message-here'      => 'Escribe tu mensaje aquí',
                'attachments-helper-text' => 'Tamaño máximo de archivo: 10MB. Tipos permitidos: Imágenes, PDF, Word, Excel, Texto',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Mensaje enviado',
                    'body'  => 'Tu mensaje ha sido enviado exitosamente.',
                ],

                'error' => [
                    'title' => 'Error al enviar mensaje',
                    'body'  => 'No se pudo enviar tu mensaje.',
                ],
            ],

            'mail' => [
                'subject' => ':record_name',
            ],
        ],
    ],
];
