<?php

return [
    'counter' => ':characters caracteres, cabe em :parts SMS (:encoding)',

    'form' => [
        'international-helper' => 'Inclua o código do país (por exemplo +91 98765 43210) ou defina um país no registro.',
    ],

    'errors' => [
        'invalid-number' => 'Número inválido: defina um país no lead/oportunidade ou inclua o código do país.',
        'not-configured' => 'Nenhum gateway de SMS configurado. Adicione os dados em Configurações → SMS.',
        'unreachable'    => 'Não foi possível conectar ao gateway de SMS.',
    ],
];
