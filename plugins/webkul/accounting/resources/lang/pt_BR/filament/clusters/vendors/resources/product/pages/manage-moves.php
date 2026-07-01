<?php

return [
    'title' => 'Entrada/Saída',

    'table' => [
        'columns' => [
            'date' => 'Data',
            'reference' => 'Referência',
            'product' => 'Produto',
            'package' => 'Embalagem',
            'lot' => 'Números de lote / série',
            'source-location' => 'Local de origem',
            'destination-location' => 'Local de destino',
            'quantity' => 'Quantidade',
            'state' => 'Estado',
            'done-by' => 'Concluído por',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Movimentação excluída',
                    'body' => 'A movimentação foi excluída com sucesso.',
                ],
            ],
        ],
    ],
];
