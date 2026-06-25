<?php

return [
    'manufacturing-manager' => [
        'unplan-order' => [
            'work-orders-already-done'    => 'Algumas ordens de trabalho já foram concluídas, portanto não é possível remover o planejamento desta ordem de produção.',
            'work-orders-already-started' => 'Algumas ordens de trabalho já foram iniciadas, portanto não é possível remover o planejamento desta ordem de produção.',
        ],
    ],

    'work-center-productivity-log' => [
        'time-tracking'                    => 'Controle de tempo: :name',
        'no-performance-productivity-loss' => 'Você precisa definir pelo menos uma perda de produtividade não arquivada na categoria \'Desempenho\'. Crie nas configurações.',
    ],

    'work-center' => [
        'already-unblocked' => 'Já foi desbloqueado.',
    ],

    'work-order' => [
        'unblock-work-center'        => 'Desbloqueie o centro de trabalho para iniciar a ordem de trabalho.',
        'already-done-or-cancelled'  => 'Você não pode iniciar uma ordem de trabalho que já esteja concluída ou cancelada',
        'no-calendar-on-work-center' => 'Não há calendário definido no centro de trabalho :name.',
        'no-productivity-loss'       => 'Você precisa definir pelo menos uma perda de produtividade na categoria \'Produtividade\'. Crie nas configurações.',
        'no-performance-loss'        => 'Você precisa definir pelo menos uma perda de produtividade na categoria \'Desempenho\'. Crie nas configurações.',
        'impossible-to-plan'         => 'Impossível planejar a ordem de trabalho. Verifique as disponibilidades do centro de trabalho.',
    ],

    'order' => [
        'product-in-byproducts'                    => 'Você não pode ter :product como produto acabado e nos subprodutos',
        'missing-lot-serial-number'                => 'Você precisa fornecer Número de lote/série para os produtos e "consumi-los": :missing_products',
        'serial-number-already-produced'           => 'Este número de série do produto :product já foi produzido',
        'byproduct-serial-number-already-produced' => 'O número de série :number usado no subproduto :product já foi produzido',
        'component-serial-number-consumed'         => 'O número de série :number usado no componente :component já foi consumido',
        'components-availability'                  => [
            'available'     => 'Disponível',
            'not-available' => 'Indisponível',
            'expected'      => 'Esperado em :date',
        ],
    ],
];
