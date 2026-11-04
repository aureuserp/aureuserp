<?php

return [
    'title'       => 'Fale conosco',
    'intro'       => 'Fale conosco sobre qualquer assunto relacionado à nossa empresa ou serviços.',
    'intro-reply' => 'Faremos o possível para responder o quanto antes.',
    'submit'      => 'Enviar',
    'lead-note'   => 'Criado pelo formulário Fale conosco do site.',

    'fields' => [
        'name'     => 'Nome',
        'phone'    => 'Telefone',
        'email'    => 'E-mail',
        'company'  => 'Empresa',
        'subject'  => 'Assunto',
        'question' => 'Pergunta',
    ],

    'validation' => [
        'no-markup' => 'Digite apenas texto simples. HTML e scripts não são permitidos.',
        'phone'     => 'Digite um número de telefone válido (dígitos, espaços, +, -, parênteses).',
    ],

    'notifications' => [
        'failed'   => 'Não foi possível enviar sua mensagem. Tente novamente mais tarde.',
        'too-many' => 'Mensagens demais. Tente novamente em :minutes minuto(s).',

        'sent' => [
            'title' => 'Obrigado!',
            'body'  => 'Sua mensagem foi enviada. Retornaremos em breve.',
        ],
    ],

    'mail' => [
        'subject' => 'Contato do site: :subject',
        'intro'   => 'Uma nova mensagem foi enviada pelo formulário Fale conosco do site.',
    ],
];
