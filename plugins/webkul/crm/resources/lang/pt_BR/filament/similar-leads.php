<?php

return [
    'navigation' => [
        'label' => 'Leads semelhantes',
    ],

    'page' => [
        'title' => 'Leads semelhantes: :name',

        'empty' => [
            'heading'     => 'Nenhum registro semelhante',
            'description' => 'Nenhum outro lead ou oportunidade tem o mesmo e-mail, telefone ou cliente.',
        ],
    ],

    'badges' => [
        'lost' => 'Perdida',
    ],

    'merge' => [
        'label'        => 'Mesclar',
        'heading'      => 'Mesclar leads e oportunidades semelhantes',
        'submit'       => 'Mesclar',
        'description'  => 'Os campos vazios do registro mantido são preenchidos com os dos outros, e suas notas, tags, mensagens, atividades e anexos passam para ele. Em seguida, os outros registros são excluídos. O resultado é uma oportunidade se algum deles for, e fica ativo mesmo que algum estivesse perdido.',
        'chatter-note' => 'Mesclados neste registro: :records',

        'fields' => [
            'records'         => 'Registros a mesclar',
            'head'            => 'Registro a manter',
            'head-helper'     => 'Seus dados são mantidos; os campos vazios são preenchidos com os outros registros.',
            'salesperson'     => 'Vendedor',
            'team'            => 'Equipe de vendas',
            'conflict-helper' => 'Os registros selecionados têm valores diferentes; escolha o que o registro mesclado manterá.',
        ],

        'notifications' => [
            'invalid'     => 'Escolha pelo menos dois registros e o que será mantido.',
            'forbidden'   => 'Você não tem permissão para mesclar estes registros (é preciso editar o registro mantido e excluir permanentemente os outros).',
            'companies'   => 'Só é possível mesclar registros da mesma empresa.',
            'choose-user' => 'Escolha o vendedor do registro mesclado.',
            'choose-team' => 'Escolha a equipe de vendas do registro mesclado.',

            'merged' => [
                'title' => 'Registros mesclados',
                'body'  => ':count registro(s) mesclado(s) em ":name".',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Lead',
            'type'        => 'Tipo',
            'contact'     => 'Nome do contato',
            'email'       => 'E-mail',
            'phone'       => 'Telefone',
            'stage'       => 'Etapa',
            'salesperson' => 'Vendedor',
            'team'        => 'Equipe de vendas',
            'created-at'  => 'Criado em',
        ],
    ],

    'graph' => [
        'heading' => 'Registros por etapa',
        'dataset' => 'Registros',
    ],
];
