<?php

return [
    'inventory-manager' => [
        'check-availability' => [
            'no-moves' => 'Nothing to check the availability for.',
        ],

        'cancel-move' => [
            'already-done' => 'You cannot cancel a stock move that has been set to \'Done\'. Create a return in order to reverse the moves which took place.',
        ],

        'unreserve-move' => [
            'already-done' => "You can not unreserve a stock move that has been set to 'Done'.",
        ],

        'validate' => [
            'quantity-rounding-mismatch' => 'The quantity done for the product ":product" doesn\'t respect the rounding precision defined on the unit of measure ":unit". Please change the quantity done or the rounding precision of your unit of measure.',
            'no-negative-quantities'     => 'No negative quantities allowed',
            'missing-lot-serial-number'  => "You need to supply a Lot/Serial Number for product:\n:products",
        ],

        'run-procurement' => [
            'no-rule-found'      => "No rule has been found to replenish \":product\" in \":location\".\nVerify the routes configuration on the product.",
            'no-source-location' => 'No source location defined on stock rule: :name!',
            'no-vendor-price'    => 'There is no matching vendor price to generate the purchase order for product :product (no vendor defined, minimum quantity not reached, dates not valid, ...). Go on the product form and complete the list of vendors.',
        ],

        'return' => [
            'origin' => 'Return of :operation_name',
        ],
    ],

    'move-line' => [
        'negative-quantity-not-allowed' => 'Reserving a negative quantity is not allowed.',
    ],

    'product-quantity' => [
        'quantity-not-set'                 => 'Quantity or Reserved Quantity should be set.',
        'removal-strategy-not-implemented' => 'Removal strategy :strategy not implemented.',
        'unreserve-more-than-stock'        => 'It is not possible to unreserve more products of :name than you have in stock.',
    ],

    'product' => [
        'endless-loop-rule' => "Invalid rule's configuration, the following rule causes an endless loop: :name",
    ],

    'move' => [
        'quantity-rounding-mismatch' => 'The quantity done for the product :product doesn\'t respect the rounding precision defined on the unit of measure :unit. Please change the quantity done or the rounding precision of your unit of measure.',
        'split-done-or-cancel'       => 'You cannot split a stock move that has been set to \'Done\' or \'Cancel\'.',
        'split-draft'                => 'You cannot split a draft move. It needs to be confirmed first.',
        'serial-already-assigned'    => 'The serial number has already been assigned to the Product: :product, Serial Number: :serial_number',
    ],

    'rule' => [
        'delay-on'     => 'Delay on :name',
        'days'         => '+ :days day(s)',
        'time-horizon' => 'Time Horizon',
    ],
];
