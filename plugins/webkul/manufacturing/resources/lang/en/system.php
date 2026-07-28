<?php

return [
    'manufacturing-manager' => [
        'unplan-order' => [
            'work-orders-already-done'    => "Some work orders are already done, so you cannot un-plan this manufacturing order.\n\nIt'd be a shame to waste all that progress, right?",
            'work-orders-already-started' => "Some work orders have already started, so you cannot un-plan this manufacturing order.\n\nIt'd be a shame to waste all that progress, right?",
        ],
    ],

    'work-center-productivity-log' => [
        'time-tracking'                    => 'Time Tracking: :name',
        'no-performance-productivity-loss' => "You need to define at least one un archive productivity loss in the category 'Performance'. Create from configuration settings.",
    ],

    'work-center' => [
        'already-unblocked' => 'It has already been unblocked.',
    ],

    'work-order' => [
        'unblock-work-center'        => 'Please unblock the work center to start the work order.',
        'already-done-or-cancelled'  => 'You cannot start a work order that is already done or cancelled',
        'no-calendar-on-work-center' => 'There is no defined calendar on work center :name.',
        'no-productivity-loss'       => "You need to define at least one productivity loss in the category 'Productivity'. Create Configuration settings.",
        'no-performance-loss'        => "You need to define at least one productivity loss in the category 'Performance'. Create Configuration settings.",
        'impossible-to-plan'         => 'Impossible to plan the work order. Please check the work center availabilities.',
    ],

    'order' => [
        'product-in-byproducts'                    => 'You cannot have :product as the finished product and in the Byproducts',
        'missing-lot-serial-number'                => 'You need to supply Lot/Serial Number for products and "consume" them: :missing_products',
        'serial-number-already-produced'           => 'This serial number for product :product has already been produced',
        'byproduct-serial-number-already-produced' => 'The serial number :number used for byproduct :product has already been produced',
        'component-serial-number-consumed'         => 'The serial number :number used for component :component has already been consumed',
        'components-availability'                  => [
            'available'     => 'Available',
            'not-available' => 'Not Available',
            'expected'      => 'Expected :date',
        ],
    ],
];
