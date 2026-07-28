<?php

return [
    'columns' => [
        'number' => 'Número',
        'state' => 'Estado',
        'customer' => 'Cliente',
        'invoice-date' => 'Data da fatura',
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
        'invoice-currency' => 'Moeda da fatura',
    ],

    'values' => [
        'yes' => 'Sim',
        'no' => 'Não',
    ],

    'notification' => [
        'completed' => 'A exportação da sua fatura foi concluída e :count linha(s) exportada(s).',
        'failed' => ':count linha(s) falharam ao exportar.',
    ],
];
