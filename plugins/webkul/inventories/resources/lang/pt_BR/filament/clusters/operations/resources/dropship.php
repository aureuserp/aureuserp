<?php

return [
    'navigation' => [
        'title' => 'Envios diretos',
        'group' => 'Transferências',
    ],

    'global-search' => [
        'partner' => 'Parceiro',
        'origin'  => 'Origem',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Dropship excluído',
                        'body'  => 'O dropship foi excluído com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir o dropship',
                        'body'  => 'O dropship não pode ser excluído porque está em uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Envios diretos excluídos',
                        'body'  => 'Os dropships foram excluídos com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir os dropships',
                        'body'  => 'Os dropships não podem ser excluídos porque estão em uso.',
                    ],
                ],
            ],
        ],
    ],
];
