<?php

return [
    'model-label'        => 'Etapa',
    'plural-model-label' => 'Etapas',

    'navigation' => [
        'title' => 'Etapas',
        'group' => 'Funil',
    ],

    'form' => [
        'fields' => [
            'teams'                    => 'Equipes de vendas',
            'teams-helper-text'        => 'Equipes cujo pipeline mostra esta etapa. Deixe vazio para compartilhá-la com todas as equipes.',
            'all-teams'                => 'Todas as equipes',
            'name'                     => 'Nome da etapa',
            'is-won'                   => 'É etapa de ganho?',
            'is-won-helper-text'       => 'As oportunidades movidas para esta etapa são marcadas como ganhas (probabilidade de 100%).',
            'requirements'             => 'Requisitos',
            'requirements-helper-text' => 'Orientação interna sobre o que deve ser feito antes de mover uma oportunidade para esta etapa.',
        ],
    ],

    'table' => [
        'columns' => [
            'teams'         => 'Equipes de vendas',
            'name'          => 'Nome da etapa',
            'is-won'        => 'Etapa de ganho',
            'requirements'  => 'Requisitos',
            'opportunities' => 'Oportunidades',
            'created-at'    => 'Criado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etapa atualizada',
                    'body'  => 'A etapa foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etapa excluída',
                    'body'  => 'A etapa foi excluída com sucesso.',
                ],
            ],
        ],
    ],
];
