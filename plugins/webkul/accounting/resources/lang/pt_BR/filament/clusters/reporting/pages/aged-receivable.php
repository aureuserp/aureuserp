<?php

return [
    'heading' => ':report - Em :date',
    'as-of'   => 'Em :date',

    'table' => [
        'columns' => [
            'at-date'      => 'A vencer',
            'period-range' => ':from-:to',
            'older'        => 'Mais antigo',
            'older-than'   => ':days+',
        ],
    ],

    'totals' => [
        'total' => 'Total de contas a receber por vencimento',
    ],

    'messages' => [
        'unposted-entries' => 'Existem lançamentos contábeis não publicados anteriores ou incluídos neste período.',
        'empty'            => 'Nenhuma conta a receber encontrada para os critérios selecionados.',
    ],

    'footer' => [
        'generated-on' => 'Gerado em :date',
    ],
];
