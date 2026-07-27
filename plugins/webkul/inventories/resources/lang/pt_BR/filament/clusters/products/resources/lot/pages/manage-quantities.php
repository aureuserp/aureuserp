<?php

return [
    'title' => 'Locais',

    'table' => [
        'columns' => [
            'product' => 'Produto',
            'location' => 'Localização',
            'storage-category' => 'Categoria de armazenamento',
            'quantity' => 'Quantidade',
            'package' => 'Embalagem',
            'on-hand' => 'Quantidade em estoque',
            'unit' => 'Unidade',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Quantidade excluída',
                    'body' => 'A quantidade foi excluída com sucesso.',
                ],
            ],
        ],
    ],
];
