<?php

return [
    'form' => [
        'fields' => [
            'accrual-amount' => 'Valor de acúmulo',
            'accrual-value-type' => 'Tipo de valor de acúmulo',
            'accrual-frequency' => 'Frequência de acúmulo',
            'accrual-day' => 'Dia de acúmulo',
            'day-of-month' => 'Dia do mês',
            'first-day-of-month' => 'Primeiro dia do mês',
            'second-day-of-month' => 'Segundo dia do mês',
            'first-period-month' => 'Mês do primeiro período',
            'first-period-day' => 'Dia do primeiro período',
            'second-period-month' => 'Mês do segundo período',
            'second-period-day' => 'Dia do segundo período',
            'first-period-year' => 'Ano do primeiro período',
            'cap-accrued-time' => 'Limitar tempo acumulado',
            'days' => 'Dias',
            'start-count' => 'Iniciar contagem',
            'start-type' => 'Tipo de início',
            'action-with-unused-accruals' => 'Ação com acúmulos não utilizados',
            'milestone-cap' => 'Limite por marco',
            'maximum-leave-yearly' => 'Máximo anual de licença',
            'accrual-validity' => 'Validade do acúmulo',
            'accrual-validity-count' => 'Contagem da validade do acúmulo',
            'accrual-validity-type' => 'Tipo de validade do acúmulo',
            'advanced-accrual-settings' => 'Configurações avançadas de acúmulo',
            'after-allocation-start' => 'Após a data inicial da alocação',
            'to-date' => 'Até a data',
        ],
    ],

    'table' => [
        'columns' => [
            'accrual-amount' => 'Valor de acúmulo',
            'accrual-value-type' => 'Tipo de valor de acúmulo',
            'frequency' => 'Frequência',
            'maximum-leave-days' => 'Máximo de dias de licença',
        ],

        'groups' => [
            'accrual-amount' => 'Valor de acúmulo',
            'accrual-value-type' => 'Tipo de valor de acúmulo',
            'frequency' => 'Frequência',
            'maximum-leave-days' => 'Máximo de dias de licença',
        ],

        'filters' => [
            'accrual-frequency' => 'Frequência de acúmulo',
            'start-type' => 'Tipo de início',
            'cap-accrued-time' => 'Limitar tempo acumulado',
            'action-with-unused-accruals' => 'Ação com acúmulos não utilizados',
            'accrual-amount' => 'Valor de acúmulo',
            'accrual-frequency' => 'Frequência de acúmulo',
            'start-type' => 'Tipo de início',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'header-actions' => [
            'created' => [
                'title' => 'Novo plano de acúmulo de licença',

                'notification' => [
                    'title' => 'Plano de acúmulo de licença criado',
                    'body' => 'O plano de acúmulo de licença foi criado com sucesso.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Plano de acúmulo de licença atualizado',
                    'body' => 'O plano de acúmulo de licença foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plano de acúmulo de licença excluído',
                    'body' => 'O plano de acúmulo de licença foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [

            'delete' => [
                'notification' => [
                    'title' => 'Planos de acúmulo de licença excluídos',
                    'body' => 'Os planos de acúmulo de licença foram excluídos com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'accrual-amount' => 'Valor de acúmulo',
            'accrual-value-type' => 'Tipo de valor de acúmulo',
            'accrual-frequency' => 'Frequência de acúmulo',
            'accrual-day' => 'Dia de acúmulo',
            'day-of-month' => 'Dia do mês',
            'first-day-of-month' => 'Primeiro dia do mês',
            'second-day-of-month' => 'Segundo dia do mês',
            'first-period-month' => 'Mês do primeiro período',
            'first-period-day' => 'Dia do primeiro período',
            'second-period-month' => 'Mês do segundo período',
            'second-period-day' => 'Dia do segundo período',
            'first-period-year' => 'Ano do primeiro período',
            'cap-accrued-time' => 'Limitar tempo acumulado',
            'days' => 'Dias',
            'start-count' => 'Iniciar contagem',
            'start-type' => 'Tipo de início',
            'action-with-unused-accruals' => 'Ação com acúmulos não utilizados',
            'milestone-cap' => 'Limite por marco',
            'maximum-leave-yearly' => 'Máximo anual de licença',
            'accrual-validity' => 'Validade do acúmulo',
            'accrual-validity-count' => 'Contagem da validade do acúmulo',
            'accrual-validity-type' => 'Tipo de validade do acúmulo',
            'advanced-accrual-settings' => 'Configurações avançadas de acúmulo',
            'after-allocation-start' => 'Após a data inicial da alocação',
        ],
    ],
];
