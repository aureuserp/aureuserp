<?php

return [
    'title'        => 'Asset Warehouse',
    'deletion'     => [
        'referenced' => 'Cannot delete a warehouse that is referenced by custody records or assets.',
    ],
    'deactivation' => [
        'has-assets' => 'Cannot deactivate a warehouse while it holds stored assets.',
    ],
    'company'      => [
        'immutable' => 'Cannot change company for a warehouse that is referenced by custody records or assets.',
    ],
];
