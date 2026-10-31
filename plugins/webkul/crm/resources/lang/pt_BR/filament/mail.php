<?php

return [
    'alias' => [
        'section'             => 'Alias de e-mail',
        'section-description' => 'E-mails enviados para este endereço criam leads nesta equipe.',
        'name'                => 'Alias de e-mail',
        'name-helper'         => 'A parte antes do @, ex.: "vendas".',
        'name-invalid'        => 'Use apenas letras, números, pontos, hífens ou sublinhados.',
        'name-taken'          => 'Este alias já é usado por outra equipe de vendas.',
        'domain'              => 'Domínio do alias',
        'domain-helper'       => 'A parte depois do @, ex.: "suaempresa.com". A caixa de um servidor de e-mail de entrada deve receber os e-mails deste domínio.',
        'domain-invalid'      => 'Informe um domínio como suaempresa.com.',
        'contact'             => 'Aceitar e-mails de',
    ],

    'reasons' => [
        'no-sender'   => 'O e-mail não tem um endereço de remetente válido.',
        'auto-reply'  => 'Resposta automática (ausência, devolução ou lista de e-mail).',
        'from-alias'  => 'Enviado de um dos aliases das equipes de vendas (loop de e-mail).',
        'no-alias'    => 'Não foi enviado para nenhum alias de equipe de vendas.',
        'not-allowed' => 'O alias não permite este remetente ("Aceitar e-mails de": :contact).',
    ],

    'errors' => [
        'no-imap'    => 'A extensão imap do PHP não está instalada no servidor.',
        'connection' => 'Não foi possível conectar ao servidor de e-mail.',
    ],

    'chatter' => [
        'from' => 'De:',
    ],

    'no-subject' => 'E-mail de :email',
];
