<?php

return [
    'label' => 'Criar fatura de fornecedor',

    'action' => [
        'notification' => [
            'warning' => [
                'title' => 'Nenhuma linha faturável',
                'body'  => 'Não há nenhuma linha faturável. Verifique se uma quantidade foi recebida.',
            ],

            'missing-journal' => [
                'title' => 'A contabilidade não está configurada',
            ],

            'success' => [
                'title' => 'Fatura de fornecedor criada',
                'body'  => 'A fatura de fornecedor foi criada com sucesso.',
            ],
        ],
    ],
];
