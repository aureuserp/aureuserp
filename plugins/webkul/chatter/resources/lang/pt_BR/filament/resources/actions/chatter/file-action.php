<?php

return [
    'setup' => [
        'title' => 'Anexos',
        'tooltip' => 'Enviar anexos',

        'modal-submit-action-label' => 'Enviar',

        'form' => [
            'fields' => [
                'files' => 'Arquivos',
                'attachment-helper-text' => 'Tamanho máximo do arquivo: 10MB. Tipos permitidos: imagens, PDF, Word, Excel, texto',

                'actions' => [
                    'delete' => [
                        'title' => 'Arquivo excluído',
                        'body' => 'O arquivo foi excluído com sucesso.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Anexos enviados',
                    'body' => 'Anexos enviados com sucesso.',
                ],

                'warning'  => [
                    'title' => 'Nenhum arquivo novo',
                    'body' => 'Todos os arquivos já foram enviados.',
                ],

                'error' => [
                    'title' => 'Erro no envio de anexo',
                    'body' => 'Falha ao enviar anexos ',
                ],
            ],
        ],
    ],
];
