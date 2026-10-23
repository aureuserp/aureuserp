<?php

return [
    'model-label'        => 'Servidor de e-mail de entrada',
    'plural-model-label' => 'Servidores de e-mail de entrada',

    'navigation' => [
        'title' => 'Servidores de e-mail de entrada',
        'group' => 'E-mail',
    ],

    'header-actions' => [
        'create' => [
            'label'        => 'Novo servidor de e-mail',
            'notification' => 'Servidor de e-mail criado',
        ],
    ],

    'form' => [
        'name'                        => 'Nome',
        'name-placeholder'            => 'ex.: Caixa de vendas',
        'host'                        => 'Servidor (host IMAP)',
        'encryption'                  => 'Criptografia',
        'port'                        => 'Porta',
        'folder'                      => 'Pasta',
        'username'                    => 'Usuário',
        'password'                    => 'Senha',
        'password-helper'             => 'Deixe vazio para manter a senha atual.',
        'validate-certificate'        => 'Validar certificado SSL',
        'validate-certificate-helper' => 'Desative apenas para servidores com certificado autoassinado.',
        'active'                      => 'Ativo',
        'active-helper'               => 'Verificado a cada minuto em busca de novos e-mails.',

        'encryptions' => [
            'ssl'  => 'SSL/TLS (porta 993)',
            'tls'  => 'STARTTLS (porta 143)',
            'none' => 'Nenhuma (porta 143)',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nome',
            'host'         => 'Servidor',
            'folder'       => 'Pasta',
            'active'       => 'Ativo',
            'last-fetched' => 'Última verificação',
            'last-error'   => 'Último erro',
        ],

        'actions' => [
            'test' => [
                'label'   => 'Testar conexão',
                'success' => 'Conexão bem-sucedida: :count e-mail(s) na pasta.',
                'failure' => 'Falha na conexão',
            ],

            'fetch' => [
                'label'        => 'Buscar agora',
                'queued'       => 'Buscando e-mails',
                'queued-body'  => 'Os novos e-mails são lidos em segundo plano (o worker da fila precisa estar em execução). Veja o resultado em E-mails recebidos.',
            ],

            'edit' => [
                'notification' => 'Servidor de e-mail atualizado',
            ],

            'delete' => [
                'notification' => 'Servidor de e-mail excluído',
            ],
        ],

        'empty' => [
            'heading'     => 'Nenhum servidor de e-mail de entrada',
            'description' => 'Adicione a caixa que recebe os e-mails enviados aos aliases das suas equipes de vendas.',
        ],
    ],
];
