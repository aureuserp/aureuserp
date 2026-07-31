<?php

return [
    'label' => 'Validar',
    'modal-heading' => 'Criar pedido pendente?',
    'modal-description' => 'Crie um pedido pendente se os produtos restantes serão processados depois. Caso contrário, não gere um pedido pendente.',

    'extra-modal-footer-actions' => [
        'no-backorder' => [
            'label' => 'Sem pedido pendente',
        ],
    ],

    'notification' => [
        'error' => [
            'title' => 'Falha na validação',
        ],

        'warning' => [
            'lines-missing' => [
                'title' => 'Nenhuma quantidade está reservada',
                'body' => 'Nenhuma quantidade está reservada para a transferência.',
            ],

            'no-quantities-reserved' => [
                'title' => 'Nenhuma quantidade está reservada',
                'body' => 'Nenhuma quantidade está reservada para a transferência.',
            ],

            'lot-missing' => [
                'title' => 'Informar lote/número de série',
                'body' => 'Você precisa informar um lote/número de série para os produtos :products.',
            ],

            'serial-qty' => [
                'title' => 'Número de série já atribuído',
                'body' => 'O número de série já foi atribuído a outro produto.',
            ],

            'partial-package' => [
                'title' => 'Não é possível movimentar o mesmo conteúdo da embalagem',
                'body' => 'Você não pode movimentar o mesmo conteúdo da embalagem mais de uma vez em uma única transferência nem dividir a embalagem entre dois locais.',
            ],
        ],
    ],
];
