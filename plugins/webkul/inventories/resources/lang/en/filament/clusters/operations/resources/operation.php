<?php

return [
    'navigation' => [
        'title' => 'Products',
        'group' => 'Inventory',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'receive-from'         => 'Receive From',
                    'contact'              => 'Contact',
                    'delivery-address'     => 'Delivery Address',
                    'operation-type'       => 'Operation Type',
                    'source-location'      => 'Source Location',
                    'destination-location' => 'Destination Location',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title' => 'Operations',

                'columns' => [
                    'product'        => 'Product',
                    'final-location' => 'Final Location',
                    'description'    => 'Description',
                    'scheduled-at'   => 'Scheduled At',
                    'deadline'       => 'Deadline',
                    'packaging'      => 'Packaging',
                    'demand'         => 'Demand',
                    'quantity'       => 'Quantity',
                    'unit'           => 'Unit',
                    'picked'         => 'Picked',
                ],

                'fields' => [
                    'product'        => 'Product',
                    'final-location' => 'Final Location',
                    'description'    => 'Description',
                    'scheduled-at'   => 'Scheduled At',
                    'deadline'       => 'Deadline',
                    'packaging'      => 'Packaging',
                    'demand'         => 'Demand',
                    'quantity'       => 'Quantity',
                    'unit'           => 'Unit',
                    'picked'         => 'Picked',

                    'lines' => [
                        'modal-heading' => 'Manage Stock Moves',
                        'add-line'      => 'Add Line',

                        'fields' => [
                            'lot'       => 'Lot/Serial Number',
                            'pick-from' => 'Pick From',
                            'location'  => 'Store To',
                            'package'   => 'Destination Package',
                            'quantity'  => 'Quantity',
                            'uom'       => 'Unit of Measure',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Additional',

                'fields' => [
                    'responsible'                  => 'Responsible',
                    'shipping-policy'              => 'Shipping Policy',
                    'shipping-policy-hint-tooltip' => 'It defines whether goods should be delivered partially or all at once.',
                    'scheduled-at'                 => 'Scheduled At',
                    'scheduled-at-hint-tooltip'    => 'The scheduled time for processing the first part of the shipment. Manually setting a value here will apply it as the expected date for all stock moves.',
                    'source-document'              => 'Source Document',
                    'source-document-hint-tooltip' => 'Reference of the document',
                ],
            ],

            'note' => [
                'title' => 'Note',

                'fields' => [

                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'       => 'Reference',
            'from'            => 'From',
            'to'              => 'To',
            'contact'         => 'Contact',
            'responsible'     => 'Responsible',
            'scheduled-at'    => 'Scheduled At',
            'deadline'        => 'Deadline',
            'closed-at'       => 'Closed At',
            'source-document' => 'Source Document',
            'operation-type'  => 'Operation Type',
            'company'         => 'Company',
            'state'           => 'State',
            'deleted-at'      => 'Deleted At',
            'created-at'      => 'Created At',
            'updated-at'      => 'Updated At',
        ],

        'groups' => [
            'state'           => 'State',
            'source-document' => 'Source Document',
            'operation-type'  => 'Operation Type',
            'scheduled-at'    => 'Schedule At',
            'created-at'      => 'Created At',
        ],

        'filters' => [
            'name'                 => 'Name',
            'state'                => 'State',
            'partner'              => 'Partner',
            'responsible'          => 'Responsible',
            'owner'                => 'Owner',
            'source-location'      => 'Source Location',
            'destination-location' => 'Destination Location',
            'deadline'             => 'Deadline',
            'scheduled-at'         => 'Scheduled At',
            'closed-at'            => 'Closed At',
            'created-at'           => 'Created At',
            'updated-at'           => 'Updated At',
            'company'              => 'Company',
            'creator'              => 'Creator',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General Information',
                'entries' => [
                    'contact'              => 'Contact',
                    'operation-type'       => 'Operation Type',
                    'source-location'      => 'Source Location',
                    'destination-location' => 'Destination Location',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title'   => 'Operations',
                'entries' => [
                    'product'        => 'Product',
                    'final-location' => 'Final Location',
                    'description'    => 'Description',
                    'scheduled-at'   => 'Scheduled At',
                    'deadline'       => 'Deadline',
                    'packaging'      => 'Packaging',
                    'demand'         => 'Demand',
                    'quantity'       => 'Quantity',
                    'unit'           => 'Unit',
                    'picked'         => 'Picked',
                ],
            ],
            'additional' => [
                'title'   => 'Additional Information',
                'entries' => [
                    'responsible'     => 'Responsible',
                    'shipping-policy' => 'Shipping Policy',
                    'scheduled-at'    => 'Scheduled At',
                    'source-document' => 'Source Document',
                ],
            ],
            'note' => [
                'title' => 'Note',
            ],
        ],
    ],

    'tabs' => [
        'todo'     => 'To Do',
        'my'       => 'My Transfers',
        'starred'  => 'Starred',
        'draft'    => 'Draft',
        'waiting'  => 'Waiting',
        'ready'    => 'Ready',
        'done'     => 'Done',
        'canceled' => 'Canceled',
    ],
];
