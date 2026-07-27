<?php

return [
    'setup' => [
        'title' => 'Enviar mensagem',
        'submit-title' => 'Enviar',

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
                    'title' => 'Mensagem enviada',
                    'body' => 'Sua mensagem foi enviada com sucesso.',
                ],

                'error' => [
                    'title' => 'Erro no envio da mensagem',
                    'body' => 'Falha ao enviar sua mensagem',
                ],
            ],

            'mail' => [
                'subject' => ':record_name',
            ],
        ],
    ],
];
