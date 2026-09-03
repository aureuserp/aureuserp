<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Error updating tracking',
                    'body'  => 'You can not change the inventory tracking of a product that was already used.',
                ],

                'reordering-rules' => [
                    'title' => 'Error updating product',
                    'body'  => 'You still have some active reordering rules on this product. Please archive or delete them first.',
                ],

                'reserved' => [
                    'title' => 'Error updating tracking',
                    'body'  => 'You can not change the inventory tracking of a product that is currently reserved on a stock move. If you need to change the inventory tracking, you should first unreserve the stock move.',
                ],

                'qty-not-zero' => [
                    'title' => 'Error updating tracking',
                    'body'  => 'Available quantity should be set to zero before changing inventory tracking.',
                ],

                'track-by-update' => [
                    'title' => 'Error updating tracking',
                    'body'  => 'You have product(s) in stock that have no lot/serial number. You can assign lot/serial numbers by doing an inventory adjustment.',
                ],
            ],
        ],
    ],
];
