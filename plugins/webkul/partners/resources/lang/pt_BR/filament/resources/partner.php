<?php

return [
    'global-search' => [
        'email' => 'E-mail',
        'phone' => 'Telefone',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'company' => 'Empresa',
                    'avatar' => 'Avatar',
                    'tax-id' => 'Identificação fiscal',
                    'job-title' => 'Cargo',
                    'phone' => 'Telefone',
                    'mobile' => 'Celular',
                    'email' => 'E-mail',
                    'website' => 'Site',
                    'title' => 'Título',
                    'name' => 'Nome',
                    'short-name' => 'Nome curto',
                    'tags' => 'Tags',
                    'color' => 'Cor',
                ],

                'address' => [
                    'title' => 'Endereço',

                    'fields' => [
                        'street1' => 'Rua 1',
                        'street2' => 'Rua 2',
                        'city' => 'Cidade',
                        'zip' => 'CEP',
                        'state' => 'Estado',
                        'country' => 'País',
                        'name' => 'Nome',
                        'code' => 'Código',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Vendas e compras',

                'fields' => [
                    'responsible' => 'Responsável',
                    'responsible-hint-text' => 'Este é o vendedor interno responsável por este cliente',
                    'company-id' => 'ID da empresa',
                    'company-id-hint-text' => 'O número de registro da empresa, usado se for diferente da identificação fiscal. Deve ser único entre todos os parceiros no mesmo país.',
                    'reference' => 'Referência',
                    'industry' => 'Setor',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'parent' => 'Pai',
            'work-email' => 'E-mail de trabalho',
            'work-phone' => 'Telefone de trabalho',
        ],

        'groups' => [
            'account-type' => 'Tipo de conta',
            'parent' => 'Pai',
            'title' => 'Título',
            'job-title' => 'Cargo',
            'industry' => 'Setor',
        ],

        'filters' => [
            'account-type' => 'Tipo de conta',
            'name' => 'Nome',
            'email' => 'E-mail',
            'parent' => 'Pai',
            'title' => 'Título',
            'tax-id' => 'Identificação fiscal',
            'phone' => 'Telefone',
            'mobile' => 'Celular',
            'job-title' => 'Cargo',
            'website' => 'Site',
            'company-registry' => 'Registro da empresa',
            'responsible' => 'Responsável',
            'reference' => 'Referência',
            'parent' => 'Pai',
            'creator' => 'Criador',
            'company' => 'Empresa',
            'industry' => 'Setor',
            'industry' => 'Setor',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Contato atualizado',
                    'body' => 'O contato foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Contato restaurado',
                    'body' => 'O contato foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Contato excluído',
                    'body' => 'O contato foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contato excluído permanentemente',
                        'body' => 'O contato foi excluído permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Contato não pôde ser excluído',
                        'body' => 'O contato não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Contatos restaurados',
                    'body' => 'Os contatos foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Contatos excluídos',
                    'body' => 'Os contatos foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contatos excluídos permanentemente',
                        'body' => 'Os contatos foram excluídos permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Contatos não puderam ser excluídos',
                        'body' => 'Os contatos não podem ser excluídos porque estão em uso no momento.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'company' => 'Empresa',
                    'avatar' => 'Avatar',
                    'tax-id' => 'Identificação fiscal',
                    'job-title' => 'Cargo',
                    'phone' => 'Telefone',
                    'mobile' => 'Celular',
                    'email' => 'E-mail',
                    'website' => 'Site',
                    'title' => 'Título',
                    'name' => 'Nome',
                    'short-name' => 'Nome curto',
                    'tags' => 'Tags',
                ],

                'address' => [
                    'title' => 'Endereço',

                    'fields' => [
                        'street1' => 'Rua 1',
                        'street2' => 'Rua 2',
                        'city' => 'Cidade',
                        'zip' => 'CEP',
                        'state' => 'Estado',
                        'country' => 'País',
                        'name' => 'Nome',
                        'code' => 'Código',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Vendas e compras',

                'fields' => [
                    'responsible' => 'Responsável',
                    'responsible-hint-text' => 'Este é o vendedor interno responsável por este cliente',
                    'company-id' => 'ID da empresa',
                    'company-id-hint-text' => 'O número de registro da empresa. Use-o se for diferente da identificação fiscal. Deve ser único entre todos os parceiros de um mesmo país',
                    'reference' => 'Referência',
                    'industry' => 'Setor',
                ],
            ],
        ],
    ],
];
