<?php

return [
    'label' => 'Produzir tudo',
    'partial-label' => 'Produzir',

    'modal' => [
        'consumption-warning' => [
            'heading' => 'Aviso de consumo',
            'description' => 'Alguns produtos consumiram quantidade diferente da esperada. Deseja validar a ordem de produção com as quantidades atuais?',

            'form' => [
                'product' => 'Produto',
                'to-consume' => 'A consumir',
                'consumed' => 'Consumido',
                'uom' => 'Unidade de medida',
            ],

            'actions' => [
                'confirm' => [
                    'label' => 'Confirmar',
                ],

                'set-quantities' => [
                    'label' => 'Definir quantidades e confirmar',
                ],
            ],
        ],

        'produced-warning' => [
            'heading' => 'Produzido é diferente do esperado',
            'description' => 'A quantidade produzida é diferente da esperada. Deseja confirmar a ordem de produção com a quantidade atual?',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Ordem de produção concluída',
            'body' => 'A ordem de produção foi concluída com sucesso.',
        ],
    ],
];
