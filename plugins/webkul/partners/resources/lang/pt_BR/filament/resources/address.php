<?php

return [
    'form' => [
        'partner' => 'Parceiro',
        'name' => 'Nome',
        'email' => 'E-mail',
        'phone' => 'Telefone',
        'mobile' => 'Celular',
        'type' => 'Tipo',
        'address' => 'Endereço',
        'city' => 'Cidade',
        'street1' => 'Rua 1',
        'street2' => 'Rua 2',
        'state' => 'Estado',
        'zip' => 'CEP',
        'code' => 'Código',
        'country' => 'País',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Adicionar endereço',

                'notification' => [
                    'title' => 'Endereço criado',
                    'body' => 'O endereço foi criado com sucesso.',
                ],
            ],
        ],

        'columns' => [
            'type' => 'Tipo',
            'name' => 'Nome do contato',
            'address' => 'Endereço',
            'city' => 'Cidade',
            'street1' => 'Rua 1',
            'street2' => 'Rua 2',
            'state' => 'Estado',
            'zip' => 'CEP',
            'country' => 'País',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Endereço atualizado',
                    'body' => 'O endereço foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Endereço excluído',
                    'body' => 'O endereço foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Endereços excluídos',
                    'body' => 'Os endereços foram excluídos com sucesso.',
                ],
            ],
        ],
    ],
];
