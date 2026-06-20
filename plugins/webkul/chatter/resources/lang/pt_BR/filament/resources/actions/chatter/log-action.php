<?php

return [
    'setup' => [
        'title' => 'Registrar nota',
        'submit-title' => 'Registrar',

        'form' => [
            'fields' => [
                'hide-subject' => 'Ocultar assunto',
                'add-subject' => 'Adicionar assunto',
                'subject' => 'Assunto',
                'write-message-here' => 'Escreva sua mensagem aqui',
                'attachments-helper-text' => 'Tamanho máximo do arquivo: 10MB. Tipos permitidos: imagens, PDF, Word, Excel, texto',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Nota registrada adicionada',
                    'body' => 'Sua nota registrada foi adicionada com sucesso.',
                ],

                'error' => [
                    'title' => 'Erro ao adicionar registro',
                    'body' => 'Falha ao adicionar sua nota registrada',
                ],
            ],
        ],
    ],
];
