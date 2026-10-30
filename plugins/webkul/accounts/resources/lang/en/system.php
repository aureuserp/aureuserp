<?php

return [
    'move' => [
        'no-journal-found-title' => 'Accounting is not set up',
        'no-journal-found'       => 'No journal could be found in company :company for any of those types: :types. Please set up a chart of accounts for this company first.',
    ],

    'posting' => [
        'invalid-entry'         => 'The journal entry is invalid: :field.',
        'unbalanced'            => 'Journal entry :move is not balanced: debit :debit, credit :credit.',
        'rejected-by-validator' => 'Posting was rejected: :reason',
        'move-not-postable'     => 'The journal entry cannot be posted: :reason',
        'move-not-reversible'   => 'Journal entry :move cannot be reversed.',
        'duplicate-source'      => 'The source key was already used by journal entry :move with different lines.',
    ],

    'tax-formula' => [
        'empty'                => 'The formula cannot be empty.',
        'invalid-character'    => 'The formula contains an invalid character: :character',
        'unexpected-token'     => 'The formula contains an unexpected token: :token',
        'unexpected-end'       => 'The formula ends unexpectedly.',
        'unclosed-parenthesis' => 'The formula contains an unclosed parenthesis.',
        'unknown-variable'     => 'Unknown variable ":variable". Only these variables are available: :variables',
        'unknown-function'     => 'Unknown function ":function". Only these functions are available: :functions',
    ],
];
