<?php

return [
    'setup' => [
        'title'        => 'Registrar Nota',
        'submit-title' => 'Registrar',

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
                    'title' => 'Nota de Registro añadida',
                    'body'  => 'Tu nota de registro se añadió exitosamente.',
                ],

                'error' => [
                    'title' => 'Error al añadir registro',
                    'body'  => 'No se pudo añadir tu nota de registro.',
                ],
            ],
        ],
    ],
];
