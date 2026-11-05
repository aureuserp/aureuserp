<?php

return [
    'heading' => ':report - Au :date',
    'as-of'   => 'Au :date',

    'table' => [
        'columns' => [
            'at-date'      => 'Non échu',
            'period-range' => ':from-:to',
            'older'        => 'Plus ancien',
            'older-than'   => ':days+',
        ],
    ],

    'totals' => [
        'total' => 'Total balance âgée clients',
    ],

    'messages' => [
        'unposted-entries' => 'Des écritures comptables non comptabilisées existent avant ou pendant cette période.',
        'empty'            => 'Aucune créance client trouvée pour les critères sélectionnés.',
    ],

    'footer' => [
        'generated-on' => 'Généré le :date',
    ],
];
