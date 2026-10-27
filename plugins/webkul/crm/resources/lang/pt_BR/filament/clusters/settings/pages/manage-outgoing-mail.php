<?php

return [
    'title' => 'Servidor de e-mail de saída',

    'actions' => [
        'test' => [
            'label'   => 'Enviar e-mail de teste',
            'subject' => 'E-mail de teste',
            'body'    => '<p>Seu servidor de e-mail de saída funciona.</p>',
            'sent'    => 'E-mail de teste enviado para :email.',
            'failed'  => 'Não foi possível enviar o e-mail de teste.',
        ],
    ],

    'form' => [
        'active'              => 'Usar um servidor de e-mail de saída próprio',
        'active-helper'       => 'Os e-mails enviados pelo ERP (por exemplo "Enviar e-mail" do CRM) passam por este servidor. Desativado, é usado o servidor da configuração da aplicação.',
        'host'                => 'Servidor SMTP',
        'encryption'          => 'Criptografia',
        'port'                => 'Porta',
        'username'            => 'Usuário',
        'password'            => 'Senha',
        'password-helper'     => 'Deixe em branco para manter a senha atual.',
        'from-address'        => 'Endereço do remetente',
        'from-address-helper' => 'Endereço de onde os e-mails são enviados. Vazio = o padrão da aplicação.',
        'from-name'           => 'Nome do remetente',

        'encryptions' => [
            'tls'  => 'STARTTLS (porta 587)',
            'ssl'  => 'SSL/TLS (porta 465)',
            'none' => 'Nenhuma (porta 25)',
        ],
    ],
];
