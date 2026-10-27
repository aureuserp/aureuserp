<?php

return [
    'group' => [
        'label' => 'Ações',
    ],

    'archive' => [
        'label'            => 'Arquivar',
        'description'      => 'Este registro será arquivado. Você pode encontrá-lo novamente com o filtro de arquivados e desarquivá-lo.',
        'bulk-description' => 'Os registros selecionados serão arquivados. Você pode encontrá-los novamente com o filtro de arquivados e desarquivá-los.',
        'notification'     => ':count registro(s) arquivado(s).',
    ],

    'unarchive' => [
        'label' => 'Desarquivar',
    ],

    'delete' => [
        'label' => 'Excluir',
    ],

    'duplicate' => [
        'label'        => 'Duplicar',
        'copy'         => ':name (cópia)',
        'notification' => ':count registro(s) duplicado(s).',
    ],

    'mark-lost' => [
        'label'        => 'Marcar como perdido',
        'notification' => ':count registro(s) marcado(s) como perdido(s).',
    ],

    'send-email' => [
        'label'        => 'Enviar e-mail',
        'short-label'  => 'E-mail',
        'submit'       => 'Enviar',

        'form' => [
            'recipient' => 'Para',
            'subject'   => 'Assunto',
            'body'      => 'Mensagem',
        ],

        'sent'         => 'E-mail enviado para :email.',
        'failed'       => 'Não foi possível enviar o e-mail.',
        'notification' => ':sent e-mail(s) enviado(s), :skipped registro(s) ignorado(s) (sem e-mail ou sem acesso).',
    ],

    'send-sms' => [
        'label'        => 'Enviar SMS',
        'short-label'  => 'SMS',
        'submit'       => 'Enviar SMS',

        'form' => [
            'recipient' => 'Destinatário',
            'message'   => 'Mensagem',
        ],

        'log-subject'  => 'SMS para :number',
        'sent'         => 'SMS enviado para :number.',
        'failed'       => 'Não foi possível enviar o SMS.',
        'notification' => ':sent SMS enviado(s), :skipped registro(s) ignorado(s) (sem número válido ou sem acesso).',
    ],
];
