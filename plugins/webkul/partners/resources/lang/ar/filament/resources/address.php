<?php

return [
    'form' => [
        'partner' => 'Partner',
        'name'    => 'Name',
        'email'   => 'Email',
        'phone'   => 'Phone',
        'mobile'  => 'Mobile',
        'type'    => 'Type',
        'address' => 'Address',
        'city'    => 'City',
        'street1' => 'Street 1',
        'street2' => 'Street 2',
        'state'   => 'State',
        'zip'     => 'Zip',
        'code'    => 'Code',
        'country' => 'Country',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Add Address',

                'notification' => [
                    'title' => 'Address created',
                    'body'  => 'The address has been created successfully.',
                ],
            ],
        ],

        'columns' => [
            'type'    => 'Type',
            'name'    => 'Contact Name',
            'address' => 'Address',
            'city'    => 'City',
            'street1' => 'Street 1',
            'street2' => 'Street 2',
            'state'   => 'State',
            'zip'     => 'Zip',
            'country' => 'Country',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Address updated',
                    'body'  => 'The address has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Address deleted',
                    'body'  => 'The address has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Addresses deleted',
                    'body'  => 'The addresses has been deleted successfully.',
                ],
            ],
        ],
    ],
];
