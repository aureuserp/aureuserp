<?php

return [
    'title' => 'SMS Gateway',

    'navigation' => [
        'group' => 'SMS',
    ],

    'actions' => [
        'test' => [
            'label'   => 'Send Test SMS',
            'number'  => 'Mobile number',
            'message' => 'Your SMS gateway works.',
            'sent'    => 'Test SMS sent to :number.',
            'failed'  => 'The test SMS could not be sent.',
        ],
    ],

    'form' => [
        'active'           => 'Use these gateway details',
        'active-helper'    => 'SMS sent from the ERP (for example CRM "Send SMS") go through this MSG91 account. When off, the MSG91 details from the application configuration are used.',
        'auth-key'         => 'Auth Key',
        'auth-key-helper'  => 'Leave empty to keep the current key.',
        'sender-id'        => 'Sender ID',
        'sender-id-helper' => 'Up to 11 characters, as approved by your provider.',
        'route'            => 'Route',

        'routes' => [
            'transactional' => 'Transactional',
            'promotional'   => 'Promotional',
        ],
    ],
];
