<?php

return [
    'title' => 'Habilidades',

    'navigation' => [
        'title' => 'Habilidades',
    ],

    'form' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Detalhes da habilidade',

                'fields' => [
                    'employee' => 'Funcionário',
                    'skill' => 'Habilidade',
                    'skill-level' => 'Nível',
                    'skill-type' => 'Tipo de habilidade',
                ],
            ],
            'addition-information' => [
                'title' => 'Informações adicionais',

                'fields' => [
                    'created-by' => 'Criado por',
                    'updated-by' => 'Atualizado por',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id' => 'ID',
            'employee' => 'Funcionário',
            'skill' => 'Habilidade',
            'skill-level' => 'Nível',
            'skill-type' => 'Tipo de habilidade',
            'user' => 'Usuário',
            'proficiency' => 'Proficiência',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'employee' => 'Funcionário',
            'skill' => 'Habilidade',
            'skill-level' => 'Nível',
            'skill-type' => 'Tipo de habilidade',
            'user' => 'Usuário',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'employee' => 'Funcionário',
            'skill-type' => 'Tipo de habilidade',
        ],
    ],

    'infolist' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Detalhes da habilidade',

                'entries' => [
                    'employee' => 'Funcionário',
                    'skill' => 'Habilidade',
                    'skill-level' => 'Nível',
                    'skill-type' => 'Tipo de habilidade',
                ],
            ],

            'additional-information' => [
                'title' => 'Informações adicionais',

                'entries' => [
                    'created-by' => 'Criado por',
                    'updated-by' => 'Atualizado por',
                ],
            ],
        ],
    ],
];
