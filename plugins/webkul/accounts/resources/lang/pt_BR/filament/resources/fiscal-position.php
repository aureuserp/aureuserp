<?php

return [
    'global-search' => [
        'zip-from' => 'CEP inicial',
        'zip-to' => 'CEP final',
        'name' => 'Nome',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nome',
            'foreign-vat' => 'IVA estrangeiro',
            'country' => 'País',
            'country-group' => 'Grupo de países',
            'zip-from' => 'CEP inicial',
            'zip-to' => 'CEP final',
            'detect-automatically' => 'Detectar automaticamente',
            'notes' => 'Notas',
            'company' => 'Empresa',
        ],
        'tabs' => [
            'account-mapping' => [
                'table' => [
                    'columns' => [
                        'source-account' => 'Conta de origem',
                        'destination-account' => 'Conta de destino',
                    ],
                ],

            ],
            'tax-mapping' => [
                'table' => [
                    'columns' => [
                        'tax-source' => 'Imposto de origem',
                        'tax-destination' => 'Imposto de destino',
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'company' => 'Empresa',
            'country' => 'País',
            'country-group' => 'Grupo de países',
            'created-by' => 'Criado por',
            'zip-from' => 'CEP inicial',
            'zip-to' => 'CEP final',
            'status' => 'Status',
            'detect-automatically' => 'Detectar automaticamente',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Condição de pagamento excluída',
                    'body' => 'A condição de pagamento foi excluída com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Posição fiscal excluída',
                    'body' => 'A posição fiscal foi excluída com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'foreign-vat' => 'IVA estrangeiro',
            'country' => 'País',
            'country-group' => 'Grupo de países',
            'zip-from' => 'CEP inicial',
            'zip-to' => 'CEP final',
            'detect-automatically' => 'Detectar automaticamente',
            'notes' => 'Notas',
        ],
    ],
];
