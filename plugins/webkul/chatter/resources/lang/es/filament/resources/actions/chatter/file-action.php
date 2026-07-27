<?php

return [
    'setup' => [
        'title'   => 'Adjuntos',
        'tooltip' => 'Subir adjuntos',

        'modal-submit-action-label' => 'Subir',

        'form' => [
            'fields' => [
                'files'                  => 'Archivos',
                'attachment-helper-text' => 'Tamaño máximo de archivo: 10 MB. Tipos permitidos: imágenes, PDF, Word, Excel, texto',

                'actions' => [
                    'delete' => [
                        'title' => 'Archivo eliminado',
                        'body'  => 'El archivo se ha eliminado correctamente.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Adjuntos subidos',
                    'body'  => 'Los adjuntos se han subido correctamente.',
                ],

                'warning'  => [
                    'title' => 'No hay archivos nuevos',
                    'body'  => 'Todos los archivos ya se han subido.',
                ],

                'error' => [
                    'title' => 'Error al subir el adjunto',
                    'body'  => 'No se pudieron subir los adjuntos ',
                ],
            ],
        ],
    ],
];
