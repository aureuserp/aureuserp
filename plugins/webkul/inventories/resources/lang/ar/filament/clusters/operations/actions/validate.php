<?php

return [
    'label'             => 'Validate',
    'modal-heading'     => 'Create Back Order?',
    'modal-description' => 'Create a backorder if the remaining products will be processed later. If not, do not generate a backorder.',

    'extra-modal-footer-actions' => [
        'no-backorder' => [
            'label' => 'No Backorder',
        ],
    ],

    'notification' => [
        'warning' => [
            'lines-missing' => [
                'title' => 'No quantities are reserved',
                'body'  => 'No quantities are reserved for the transfer.',
            ],

            'lot-missing' => [
                'title' => 'Supply Lot/Serial Number',
                'body'  => 'You need to supply a Lot/Serial Number for products',
            ],

            'serial-qty' => [
                'title' => 'Serial Number Already Assigned',
                'body'  => 'The serial number has already been assigned to another product.',
            ],

            'partial-package' => [
                'title' => 'Can not move same package content',
                'body'  => 'You cannot move the same package contents more than once within a single transfer or split the package between two locations.',
            ],
        ],
    ],
];
