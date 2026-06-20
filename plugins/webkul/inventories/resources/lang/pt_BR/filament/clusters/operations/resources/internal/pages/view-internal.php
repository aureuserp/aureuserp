<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Transferência interna excluída',
                    'body' => 'A transferência interna foi excluída com sucesso.',
                ],

                'error' => [
                    'title' => 'Não foi possível excluir a transferência interna',
                    'body' => 'A transferência interna não pode ser excluída porque está em uso.',
                ],
            ],
        ],
    ],
];
