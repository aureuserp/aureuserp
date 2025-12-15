<?php

return [
    'setup' => [
        'title'        => 'Log Note',
        'submit-title' => 'Log',

        'form' => [
            'fields' => [
                'hide-subject'            => 'Hide Subject',
                'add-subject'             => 'Add Subject',
                'subject'                 => 'Subject',
                'write-message-here'      => 'Write your message here',
                'attachments-helper-text' => 'Max file size: 10MB. Allowed types: Images, PDF, Word, Excel, Text',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Log Note added',
                    'body'  => 'Your log note added successfully.',
                ],

                'error' => [
                    'title' => 'Log add error',
                    'body'  => 'Failed to add your log note',
                ],
            ],
        ],
    ],
];
