<?php

return [
    'automatic'   => 'Automatic',
    'reset'       => 'Use computed probability',
    'convert-100' => 'A lead at 100% probability cannot be converted. Lower its probability first.',

    'popover' => [
        'badge'      => 'AI-computed probability',
        'chances'    => 'AI-computed chances of winning for',
        'no-team'    => 'No sales team',
        'no-history' => 'Not enough won and lost history yet to compute a probability.',
        'manual'     => 'The probability was set by hand; this is the computed one.',
        'positives'  => 'Top Positives',
        'negatives'  => 'Top Negatives',
    ],

    'factors' => [
        'stage'         => ':value stage',
        'phone-valid'   => 'Has a valid phone number',
        'phone-invalid' => 'Has no valid phone number',
        'email-valid'   => 'Has a valid email address',
        'email-invalid' => 'Has no valid email address',
        'country'       => 'Located in :value',
        'state'         => 'Located in :value',
        'source'        => 'Comes from :value',
        'tag'           => 'Tagged :value',
    ],

    'variables' => [
        'country_id'    => 'Country',
        'state_id'      => 'State',
        'phone'         => 'Phone quality',
        'email'         => 'Email quality',
        'utm_source_id' => 'Source',
        'tags'          => 'Tags',
    ],
];
