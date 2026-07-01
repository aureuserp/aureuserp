<?php

return [
    'title' => 'Gerenciar operações',

    'form' => [
        'enable-work-orders' => [
            'label' => 'Ordens de trabalho',
            'helper-text' => 'Executar operações nos centros de trabalho designados.',
            'link-text' => 'Configurar centros de trabalho',
        ],

        'enable-work-order-dependencies' => [
            'label' => 'Dependências da ordem de trabalho',
            'helper-text' => 'Defina a sequência em que as ordens de trabalho devem ser processadas. Habilite este recurso na aba Diversos de cada lista de materiais.',
        ],

        'enable-byproducts' => [
            'label' => 'Subprodutos',
            'helper-text' => 'Gerar subprodutos durante a produção (A + B → C + D).',
        ],
    ],
];
