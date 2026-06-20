<?php

return [
    'title' => 'Empresas',

    'navigation' => [
        'title' => 'Empresas',
        'group' => 'Configurações',
    ],

    'global-search' => [
        'email' => 'E-mail',
    ],

    'form' => [
        'sections' => [
            'company-information' => [
                'title' => 'Informações da empresa',
                'fields' => [
                    'name' => 'Nome da empresa',
                    'registration-number' => 'Número de registro',
                    'company-id' => 'ID da empresa',
                    'tax-id' => 'Identificação fiscal',
                    'tax-id-tooltip' => 'O ID fiscal é um identificador único da sua empresa.',
                    'website' => 'Site',
                ],
            ],

            'address-information' => [
                'title' => 'Informações de endereço',

                'fields' => [
                    'street1' => 'Rua 1',
                    'street2' => 'Rua 2',
                    'city' => 'Cidade',
                    'zipcode' => 'CEP',
                    'country' => 'País',
                    'currency-name' => 'Nome da moeda',
                    'phone-code' => 'Código telefônico',
                    'code' => 'Código',
                    'country-name' => 'Nome do país',
                    'state-required' => 'Estado obrigatório',
                    'zip-required' => 'CEP obrigatório',
                    'create-country' => 'Criar país',
                    'state' => 'Estado',
                    'state-name' => 'Nome do estado',
                    'state-code' => 'Código do estado',
                    'create-state' => 'Criar estado',
                ],
            ],

            'additional-information' => [
                'title' => 'Informações adicionais',

                'fields' => [
                    'default-currency' => 'Moeda padrão',
                    'currency-name' => 'Nome da moeda',
                    'currency-full-name' => 'Nome completo da moeda',
                    'currency-symbol' => 'Símbolo da moeda',
                    'currency-iso-numeric' => 'Número ISO da moeda',
                    'currency-decimal-places' => 'Casas decimais da moeda',
                    'currency-rounding' => 'Arredondamento da moeda',
                    'currency-status' => 'Status da moeda',
                    'company-foundation-date' => 'Data de fundação da empresa',
                    'currency-create' => 'Criar moeda',
                    'status' => 'Status',
                ],
            ],

            'branding' => [
                'title' => 'Marca',
                'fields' => [
                    'company-logo' => 'Logotipo da empresa',
                    'color' => 'Cor',
                ],
            ],

            'contact-information' => [
                'title' => 'Informações de contato',
                'fields' => [
                    'email' => 'Endereço de e-mail',
                    'phone' => 'Número de telefone',
                    'mobile' => 'Número de telefone',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo' => 'Logotipo',
            'company-name' => 'Nome da empresa',
            'branches' => 'Filiais',
            'email' => 'E-mail',
            'city' => 'Cidade',
            'country' => 'País',
            'currency' => 'Moeda',
            'status' => 'Status',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'company-name' => 'Nome da empresa',
            'city' => 'Cidade',
            'country' => 'País',
            'state' => 'Estado',
            'email' => 'E-mail',
            'phone' => 'Telefone',
            'currency' => 'Moeda',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'status' => 'Status',
            'country' => 'País',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Empresa editada',
                    'body' => 'A empresa foi editada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Empresa excluída',
                    'body' => 'A empresa foi excluída com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Empresa restaurada',
                    'body' => 'A empresa foi restaurada com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Empresas restauradas',
                    'body' => 'As empresas foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Empresas excluídas',
                    'body' => 'As empresas foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Empresas excluídas permanentemente',
                    'body' => 'As empresas foram excluídas permanentemente com sucesso.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Empresas criadas',
                    'body' => 'As empresas foram criadas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'company-information' => [
                'title' => 'Informações da empresa',
                'entries' => [
                    'name' => 'Nome da empresa',
                    'registration-number' => 'Número de registro',
                    'company-id' => 'ID da empresa',
                    'tax-id' => 'Identificação fiscal',
                    'tax-id-tooltip' => 'O ID fiscal é um identificador único da sua empresa.',
                    'website' => 'Site',
                ],
            ],

            'address-information' => [
                'title' => 'Informações de endereço',

                'entries' => [
                    'street1' => 'Rua 1',
                    'street2' => 'Rua 2',
                    'city' => 'Cidade',
                    'zipcode' => 'CEP',
                    'country' => 'País',
                    'currency-name' => 'Nome da moeda',
                    'phone-code' => 'Código telefônico',
                    'code' => 'Código',
                    'country-name' => 'Nome do país',
                    'state-required' => 'Estado obrigatório',
                    'zip-required' => 'CEP obrigatório',
                    'create-country' => 'Criar país',
                    'state' => 'Estado',
                    'state-name' => 'Nome do estado',
                    'state-code' => 'Código do estado',
                    'create-state' => 'Criar estado',
                ],
            ],

            'additional-information' => [
                'title' => 'Informações adicionais',

                'entries' => [
                    'default-currency' => 'Moeda padrão',
                    'currency-name' => 'Nome da moeda',
                    'currency-full-name' => 'Nome completo da moeda',
                    'currency-symbol' => 'Símbolo da moeda',
                    'currency-iso-numeric' => 'Número ISO da moeda',
                    'currency-decimal-places' => 'Casas decimais da moeda',
                    'currency-rounding' => 'Arredondamento da moeda',
                    'currency-status' => 'Status da moeda',
                    'company-foundation-date' => 'Data de fundação da empresa',
                    'currency-create' => 'Criar moeda',
                    'status' => 'Status',
                ],
            ],

            'branding' => [
                'title' => 'Marca',
                'entries' => [
                    'company-logo' => 'Logotipo da empresa',
                    'color' => 'Cor',
                ],
            ],

            'contact-information' => [
                'title' => 'Informações de contato',
                'entries' => [
                    'email' => 'Endereço de e-mail',
                    'phone' => 'Número de telefone',
                    'mobile' => 'Número de telefone',
                ],
            ],
        ],
    ],
];
