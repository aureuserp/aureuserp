<?php

return [
    'setup' => [
        'title'        => 'Registrar nota',
        'submit-title' => 'Registrar',

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
                    'title' => 'Nota registrada',
                    'body'  => 'Su nota se ha registrado correctamente.',
                ],

                'error' => [
                    'title' => 'Error al registrar la nota',
                    'body'  => 'No se pudo registrar su nota',
                ],
            ],
        ],
    ],
];
