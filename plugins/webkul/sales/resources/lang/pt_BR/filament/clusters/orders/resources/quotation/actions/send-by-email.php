<?php

return [
    'title' => 'Enviar por e-mail',
    'resend-title' => 'Reenviar por e-mail',
    'quotation' => 'cotação',
    'quotations' => 'cotações',

    'modal' => [
        'heading' => 'Enviar cotação por e-mail',
    ],

    'form' => [
        'fields' => [
            'partners' => 'Parceiros',
            'subject' => 'Assunto',
            'description' => 'Descrição',
            'attachment' => 'Anexo',
        ],
    ],

    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'Nenhum destinatário selecionado',
                    'body' => 'Selecione pelo menos um parceiro para enviar cotações.',
                ],

                'all_success' => [
                    'title' => 'Cotações enviadas!',
                    'body' => 'Suas :plural foram entregues com sucesso para: :recipients',
                ],

                'all_failed' => [
                    'title' => 'Não foi possível enviar as cotações',
                    'body' => 'Encontramos problemas ao enviar suas cotações: :failures',
                ],

                'partial_success' => [
                    'title' => 'Algumas cotações foram enviadas',
                    'sent_part' => 'Entregue com sucesso para: :recipients',
                    'failed_part' => 'Não foi possível entregar para: :failures',
                ],

                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],

];
