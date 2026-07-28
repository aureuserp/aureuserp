<?php

return [
    'navigation' => [
        'title' => 'Visualizar lista de preços do fornecedor',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Preço do fornecedor excluído',
                    'body' => 'O preço do fornecedor foi excluído com sucesso.',
                ],

                'error' => [
                    'title' => 'Não foi possível excluir o preço do fornecedor',
                    'body' => 'O preço do fornecedor não pode ser excluído porque está em uso.',
                ],
            ],
        ],
    ],
];
