<?php

return [
    'title'       => 'Manage Lead Scoring',
    'description' => 'The probability of winning each open lead and opportunity is computed from your won and lost history. Stage and sales team are always used.',

    'form' => [
        'variables'          => 'Also consider',
        'variables-helper'   => 'Which details of a record the probability takes into account.',
        'start-date'         => 'Consider leads created as of the',
        'start-date-helper'  => 'Leave empty to use the whole history.',
        'update'             => 'Update Scoring Probabilities',
        'update-description' => 'Saves these choices, relearns from the won and lost history and recomputes every open record.',
        'updated'            => 'Probabilities updated for :count open record(s).',
    ],
];
