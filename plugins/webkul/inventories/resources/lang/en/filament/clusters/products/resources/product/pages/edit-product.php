<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Error updating tracking',
                    'body'  => 'You can not change the inventory tracking of a product that was already used.',
                ],

                'track-by-update' => [
                    'title' => 'Error updating tracking',
                    'body'  => 'You have product(s) in stock that have no lot/serial number. You can assign lot/serial numbers by doing an inventory adjustment.',
                ],
            ],
        ],
    ],

    'header-actions' => [
        'update-quantity' => [
            'label'                     => 'Update Quantity',
            'modal-heading'             => 'Update Product Quantity',
            'modal-submit-action-label' => 'Update',

            'form' => [
                'fields' => [
                    'product'     => 'Product',
                    'on-hand-qty' => 'On Hand Quantity',
                ],
            ],
        ],
    ],
];
