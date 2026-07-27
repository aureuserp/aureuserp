<?php

return [
    'title' => 'Tipo de licença',
    'navigation' => [
        'title' => 'Tipo de licença',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',
                'fields' => [
                    'name' => 'Título',
                    'approval' => 'Aprovação',
                    'requires-allocation' => 'Requer alocação',
                    'employee-requests' => 'Solicitações de colaboradores',
                    'display-option' => 'Opção de exibição',
                ],
            ],
            'display-option' => [
                'title' => 'Opção de exibição',
                'fields' => [
                    'color' => 'Cor',
                ],
            ],
            'configuration' => [
                'title' => 'Configuração',

                'fields' => [
                    'notified-time-off-officers' => 'Responsáveis de ausências notificados',
                    'take-time-off-in' => 'Registrar ausência em',
                    'public-holiday-included' => 'Feriado incluído',
                    'allow-to-attach-supporting-document' => 'Permitir anexar documento comprobatório',
                    'show-on-dashboard' => 'Mostrar no painel',
                    'allow-negative-cap' => 'Permitir limite negativo',
                    'kind-off-time' => 'Tipo de tempo',
                    'max-negative-cap' => 'Limite negativo máximo',
                    'kind-of-time' => 'Tipo de ausência',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'company-name' => 'Empresa',
            'color' => 'Cor',
            'notified-time-officers' => 'Responsáveis de tempo notificados',
            'time-off-approval' => 'Aprovação de ausência',
            'requires-allocation' => 'Requer alocação',
            'allocation-approval' => 'Aprovação de alocação',
            'employee-request' => 'Solicitação do colaborador',
        ],

        'filters' => [
            'name' => 'Nome',
            'company-name' => 'Empresa',
            'time-off-approval' => 'Aprovação de ausência',
            'requires-allocation' => 'Requer alocação',
            'time-type' => 'Tipo de tempo',
            'request-unit' => 'Unidade da solicitação',
            'created-by' => 'Criado por',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipo de licença excluído',
                    'body' => 'O tipo de licença foi excluído com sucesso.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de licença restaurado',
                    'body' => 'O tipo de licença foi restaurado com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de licença restaurado',
                    'body' => 'O tipo de licença foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de licença excluído',
                    'body' => 'O tipo de licença foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipo de licença excluído permanentemente',
                        'body' => 'O tipo de licença foi excluído permanentemente com sucesso.',
                    ],
                    'error' => [
                        'title' => 'Tipo de licença não pôde ser excluído',
                        'body' => 'O tipo de licença não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',
                'entries' => [
                    'name' => 'Título',
                    'approval' => 'Aprovação',
                    'requires-allocation' => 'Requer alocação',
                    'employee-requests' => 'Solicitações de colaboradores',
                    'display-option' => 'Opção de exibição',
                ],
            ],
            'display-option' => [
                'title' => 'Opção de exibição',
                'entries' => [
                    'color' => 'Cor',
                ],
            ],
            'configuration' => [
                'title' => 'Configuração',

                'entries' => [
                    'notified-time-off-officers' => 'Responsáveis de ausências notificados',
                    'take-time-off-in' => 'Registrar ausência em',
                    'public-holiday-included' => 'Feriado incluído',
                    'allow-to-attach-supporting-document' => 'Permitir anexar documento comprobatório',
                    'show-on-dashboard' => 'Mostrar no painel',
                    'kind-off-time' => 'Tipo de tempo',
                    'max-negative-cap' => 'Limite negativo máximo',
                    'kind-of-time' => 'Tipo de ausência',
                ],
            ],
        ],
    ],
];
