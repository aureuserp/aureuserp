<?php

return [
    'title' => 'Manage Operations',

    'form' => [
        'enable-work-orders' => [
            'label'       => 'Work Orders',
            'helper-text' => 'Execute operations at designated work centers.',
            'link-text'   => 'Configure work centers',
        ],

        'enable-work-order-dependencies' => [
            'label'       => 'Work Order Dependencies',
            'helper-text' => 'Set the sequence in which work orders should be processed. Enable this feature from the Miscellaneous tab of each BoM.',
        ],

        'enable-byproducts' => [
            'label'       => 'Byproducts',
            'helper-text' => 'Generate by-products during production (A + B → C + D).',
        ],
    ],
];
