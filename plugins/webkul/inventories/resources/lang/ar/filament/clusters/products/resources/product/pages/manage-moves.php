<?php

return [
    'title' => 'IN/OUT',

    'tabs' => [
        'todo'     => 'To Do',
        'done'     => 'Done',
        'incoming' => 'Incoming',
        'outgoing' => 'Outgoing',
        'internal' => 'Internal',
    ],

    'table' => [
        'columns' => [
            'date'                 => 'Date',
            'reference'            => 'Reference',
            'product'              => 'Product',
            'package'              => 'Package',
            'lot'                  => 'Lot / Serial Numbers',
            'source-location'      => 'Source Location',
            'destination-location' => 'Destination Location',
            'quantity'             => 'Quantity',
            'state'                => 'State',
            'done-by'              => 'Done By',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Move deleted',
                    'body'  => 'The move has been deleted successfully.',
                ],
            ],
        ],
    ],
];
