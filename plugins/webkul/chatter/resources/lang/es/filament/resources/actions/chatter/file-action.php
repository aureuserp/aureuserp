<?php

return [
    'setup' => [
        'title'   => 'Adjuntos',
        'tooltip' => 'Subir Adjuntos',

        'form' => [
            'fields' => [
                'files'                => 'Archivos',
                'attachment-helper-text' => 'Tamaño máximo de archivo: 10MB. Tipos permitidos: Imágenes, PDF, Word, Excel, Texto',

                'actions' => [
                    'delete' => [
                        'title' => 'Archivo eliminado',
                        'body'  => 'El archivo ha sido eliminado exitosamente.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Adjuntos Subidos',
                    'body'  => 'Adjuntos subidos exitosamente.',
                ],

                'warning'  => [
                    'title' => 'No hay archivos nuevos',
                    'body'  => 'Todos los archivos ya han sido subidos.',
                ],

                'error' => [
                    'title' => 'Error al subir adjuntos',
                    'body'  => 'Error al subir adjuntos.',
                ],
            ],
        ],
    ],
];
