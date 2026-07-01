<?php

return [
    'title'        => 'Enviar por e-mail',
    'resend-title' => 'Reenviar por e-mail',
    'quotation'    => 'orçamento',
    'quotations'   => 'orçamentos',

    'modal' => [
        'heading' => 'Enviar orçamento por e-mail',
    ],

    'form' => [
        'fields' => [
            'partners'    => 'Parceiros',
            'subject'     => 'Assunto',
            'description' => 'Descrição',
            'attachment'  => 'Anexo',
        ],
    ],

    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'Nenhum destinatário selecionado',
                    'body'  => 'Selecione pelo menos um parceiro para enviar orçamentos.',
                ],

                'all_success' => [
                    'title' => 'Orçamentos enviadas!',
                    'body'  => 'Suas :plural foram entregues com sucesso para: :recipients',
                ],

                'all_failed' => [
                    'title' => 'Não foi possível enviar as orçamentos',
                    'body'  => 'Encontramos problemas ao enviar suas orçamentos: :failures',
                ],

                'partial_success' => [
                    'title'       => 'Algumas orçamentos foram enviadas',
                    'sent_part'   => 'Entregue com sucesso para: :recipients',
                    'failed_part' => 'Não foi possível entregar para: :failures',
                ],

                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],

];
