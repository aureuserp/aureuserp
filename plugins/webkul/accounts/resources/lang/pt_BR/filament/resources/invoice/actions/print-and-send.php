<?php

return [
    'title' => 'Imprimir e enviar',

    'modal' => [
        'title' => 'Visualizar fatura',

        'form' => [
            'partners' => 'Cliente',
            'subject' => 'Assunto',
            'description' => 'Descrição',
            'files' => 'Anexo',
        ],

        'action' => [
            'submit' => [
                'title' => 'Enviar',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Fatura enviada',
                'body' => 'A fatura foi enviada com sucesso.',
            ],
        ],
    ],
];
