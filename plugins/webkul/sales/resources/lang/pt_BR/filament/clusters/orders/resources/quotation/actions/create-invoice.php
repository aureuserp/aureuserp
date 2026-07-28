<?php

return [
    'title' => 'Criar fatura',

    'modal' => [
        'heading' => 'Criar fatura',
    ],

    'notification' => [
        'invoice-created' => [
            'title' => 'Fatura criada',
            'body' => 'A fatura foi criada com sucesso.',
        ],

        'no-invoiceable-lines' => [
            'title' => 'Nenhuma linha faturável',
            'body' => 'Não há nenhuma linha faturável. Verifique se uma quantidade foi recebida.',
        ],
    ],

    'form' => [
        'fields' => [
            'create-invoice' => 'Criar fatura',
        ],
    ],
];
