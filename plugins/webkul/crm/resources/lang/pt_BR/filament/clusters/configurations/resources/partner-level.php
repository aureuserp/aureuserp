<?php

return [
    'model-label'        => 'Nível',
    'plural-model-label' => 'Níveis',

    'navigation' => [
        'title' => 'Níveis',
        'group' => 'Membros',
    ],

    'header-actions' => [
        'create' => 'Novo nível',
    ],

    'form' => [
        'name'          => 'Nome',
        'weight'        => 'Peso do nível',
        'weight-helper' => 'Probabilidade de parceiros deste nível receberem leads. 0 significa nenhum.',
    ],

    'table' => [
        'columns' => [
            'name'     => 'Nome',
            'weight'   => 'Peso do nível',
            'partners' => 'Contatos',
        ],
    ],

    'actions' => [
        'archive' => 'Arquivar',
    ],

    'tabs' => [
        'active'   => 'Ativos',
        'archived' => 'Arquivados',
    ],

    'notifications' => [
        'created'  => 'Nível criado',
        'updated'  => 'Nível atualizado',
        'archived' => 'Nível arquivado',
        'restored' => 'Nível restaurado',
        'deleted'  => 'Nível excluído',
    ],

    'contact' => [
        'section'       => 'Nível do parceiro',
        'level'         => 'Nível',
        'weight'        => 'Peso do nível',
        'weight-helper' => 'Deixe vazio para usar o peso do nível.',
    ],
];
