<?php

return [
    'columns' => [
        'date' => 'Data',
        'name' => 'Nome',
        'journal' => 'Diário',
        'payment-method' => 'Método de pagamento',
        'partner' => 'Parceiro',
        'amount-currency' => 'Valor em moeda',
        'amount' => 'Valor',
        'state' => 'Estado',
        'company' => 'Empresa',
    ],

    'notification' => [
        'completed' => 'A exportação do seu pagamento foi concluída e :count linha(s) exportada(s).',
        'failed' => ':count linha(s) falharam ao exportar.',
    ],
];
