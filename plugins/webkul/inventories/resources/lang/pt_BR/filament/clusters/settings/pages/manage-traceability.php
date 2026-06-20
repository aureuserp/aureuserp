<?php

return [
    'title' => 'Gerenciar rastreabilidade',

    'form' => [
        'enable-lots-serial-numbers' => 'Lotes e números de série',
        'enable-lots-serial-numbers-helper-text' => 'Obtenha rastreabilidade completa de fornecedores a clientes',
        'configure-lots' => 'Configurar lotes',
        'enable-expiration-dates' => 'Datas de validade',
        'enable-expiration-dates-helper-text' => 'Defina datas de validade em lotes e números de série',
        'display-on-delivery-slips' => 'Exibir nos comprovantes de entrega',
        'display-on-delivery-slips-helper-text' => 'Lotes e números de série aparecerão nos comprovantes de entrega',
        'display-expiration-dates-on-delivery-slips' => 'Exibir datas de validade nos comprovantes de entrega',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'As datas de validade aparecerão no comprovante de entrega',
        'enable-consignments' => 'Consignações',
        'enable-consignments-helper-text' => 'Definir proprietário em produtos armazenados',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Você tem produtos em estoque com rastreamento por lote/número de série habilitado. ',
                'body' => 'Primeiro desative o rastreamento em todos os produtos antes de desativar esta configuração.',
            ],
        ],
    ],
];
