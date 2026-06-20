<?php

return [
    'form' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Informações gerais',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Informações da filial',

                        'fields' => [
                            'company-name' => 'Nome da empresa',
                            'registration-number' => 'Número de registro',
                            'tax-id' => 'Identificação fiscal',
                            'tax-id-tooltip' => 'O ID fiscal é um identificador único da sua empresa.',
                            'color' => 'Cor',
                            'company-id' => 'ID da empresa',
                            'company-id-tooltip' => 'O ID da empresa é um identificador único da sua empresa.',
                        ],
                    ],

                    'branding' => [
                        'title' => 'Marca',
                        'fields' => [
                            'branch-logo' => 'Logotipo da filial',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Informações de endereço',

                'sections' => [
                    'address-information' => [
                        'title' => 'Informações de endereço',

                        'fields' => [
                            'street1' => 'Rua 1',
                            'street2' => 'Rua 2',
                            'city' => 'Cidade',
                            'zip' => 'CEP',
                            'country' => 'País',
                            'country-currency-name' => 'Nome da moeda',
                            'country-phone-code' => 'Código telefônico',
                            'country-code' => 'Código',
                            'country-name' => 'Nome do país',
                            'country-state-required' => 'Estado obrigatório',
                            'country-zip-required' => 'CEP obrigatório',
                            'country-create' => 'Criar país',
                            'state' => 'Estado',
                            'state-name' => 'Nome do estado',
                            'state-code' => 'Código do estado',
                            'zip-code' => 'CEP',
                            'state-create' => 'Criar estado',
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
                            'currency-create' => 'Criar moeda',
                            'company-foundation-date' => 'Data de fundação da empresa',
                            'status' => 'Status',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Informações de contato',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Informações de contato',

                        'fields' => [
                            'email-address' => 'Endereço de e-mail',
                            'phone-number' => 'Número de telefone',
                            'mobile-number' => 'Número de telefone',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo' => 'Logotipo',
            'company-name' => 'Nome da filial',
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
            'company-name' => 'Nome da filial',
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
            'trashed' => 'Na lixeira',
            'status' => 'Status',
            'country' => 'País',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Filial criada',
                    'body' => 'A filial foi criada com sucesso.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Filial atualizada',
                    'body' => 'A filial foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Filial excluída',
                    'body' => 'A filial foi excluída com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Filial restaurada',
                    'body' => 'A filial foi restaurada com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Filiais restauradas',
                    'body' => 'As filiais foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Filiais excluídas',
                    'body' => 'As filiais foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Filiais excluídas permanentemente',
                    'body' => 'As filiais foram excluídas permanentemente com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Informações gerais',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Informações da filial',

                        'entries' => [
                            'company-name' => 'Nome da empresa',
                            'registration-number' => 'Número de registro',
                            'tax-id' => 'Identificação fiscal',
                            'registration-number-tooltip' => 'O ID fiscal é um identificador único da sua empresa.',
                            'color' => 'Cor',
                        ],
                    ],

                    'branding' => [
                        'title' => 'Marca',
                        'entries' => [
                            'branch-logo' => 'Logotipo da filial',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Informações de endereço',

                'sections' => [
                    'address-information' => [
                        'title' => 'Informações de endereço',

                        'entries' => [
                            'street1' => 'Rua 1',
                            'street2' => 'Rua 2',
                            'city' => 'Cidade',
                            'zip' => 'CEP',
                            'country' => 'País',
                            'country-currency-name' => 'Nome da moeda',
                            'country-phone-code' => 'Código telefônico',
                            'country-code' => 'Código',
                            'country-name' => 'Nome do país',
                            'country-state-required' => 'Estado obrigatório',
                            'country-zip-required' => 'CEP obrigatório',
                            'country-create' => 'Criar país',
                            'state' => 'Estado',
                            'state-name' => 'Nome do estado',
                            'state-code' => 'Código do estado',
                            'zip-code' => 'CEP',
                            'state-create' => 'Criar estado',
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
                            'currency-create' => 'Criar moeda',
                            'company-foundation-date' => 'Data de fundação da empresa',
                            'status' => 'Status',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Informações de contato',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Informações de contato',

                        'entries' => [
                            'email-address' => 'Endereço de e-mail',
                            'phone-number' => 'Número de telefone',
                            'mobile-number' => 'Número de telefone',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
