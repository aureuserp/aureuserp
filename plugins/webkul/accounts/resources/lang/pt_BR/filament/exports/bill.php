<?php

return [
    'columns' => [
        'number' => 'Número',
        'state' => 'Estado',
        'customer' => 'Cliente',
        'bill-date' => 'Data da fatura de fornecedor',
        'due-date' => 'Data de vencimento',
        'tax-excluded' => 'Imposto excluído',
        'tax' => 'Imposto',
        'total' => 'Total',
        'amount-due' => 'Valor devido',
        'payment-state' => 'Status do pagamento',
        'checked' => 'Verificado',
        'accounting-date' => 'Data contábil',
        'source-document' => 'Documento de origem',
        'reference' => 'Referência',
        'sales-person' => 'Vendedor',
        'bill-currency' => 'Moeda da fatura de fornecedor',
    ],

    'values' => [
        'yes' => 'Sim',
        'no' => 'Não',
    ],

    'notification' => [
        'completed' => 'A exportação da sua fatura de fornecedor foi concluída e :count linha(s) exportada(s).',
        'failed' => ':count linha(s) falharam ao exportar.',
    ],
];
