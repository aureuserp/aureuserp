<?php

return [
    'heading' => ':report - As of :date',
    'as-of'   => 'As of :date',

    'table' => [
        'columns' => [
            'at-date'      => 'At Date',
            'period-range' => ':from-:to',
            'older'        => 'Older',
            'older-than'   => ':days+',
        ],
    ],

    'totals' => [
        'total' => 'Total Aged Receivable',
    ],

    'messages' => [
        'unposted-entries' => 'There are unposted Journal Entries prior or included in this period.',
        'empty'            => 'No receivables found for the selected criteria.',
    ],

    'footer' => [
        'generated-on' => 'Generated on :date',
    ],
];
