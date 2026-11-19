<?php

return [
    'title' => 'Asset & Custody Site',

    'deletion' => [
        'referenced' => 'Cannot delete a site that is referenced by custody records or assets.',
    ],

    'deactivation' => [
        'open-custody' => 'Cannot deactivate a site while it holds open custody records.',
    ],

    'company' => [
        'immutable' => 'Cannot change company for a site that is referenced by custody records or assets.',
    ],

    'hierarchy' => [
        'circular-reference' => 'A site cannot be set as a child of itself or one of its descendants.',
    ],

    'usage' => [
        'has-fixed-assets'   => 'Cannot change site usage type to custody zone while it or its sub-sites have fixed assets deployed without an employee.',
        'has-custody-assets' => 'Cannot change site usage type to asset location while it or its sub-sites have active employee custody assets.',
    ],
];
